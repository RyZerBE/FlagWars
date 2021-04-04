<?php

namespace matze\flagwars\scheduler;

use BauboLP\Core\Provider\LanguageProvider;
use BauboLP\Cloud\CloudBridge;
use matze\flagwars\entity\FlagEntity;
use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\game\Team;
use matze\flagwars\provider\FlagWarsProvider;
use matze\flagwars\utils\Scoreboard;
use matze\flagwars\utils\Settings;
use pocketmine\entity\Entity;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GameUpdateTask extends Task {

    /** @var int  */
    private $points = 0;

    /** @var int  */
    private $degree = 0;

    /** @var int[]  */
    private $showNumbers = [60, 30, 15, 10, 5, 3, 2, 1];

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick): void {
        $game = GameManager::getInstance();

        if($game->isIngame()) {
            $map = $game->getMap();
            $level = Server::getInstance()->getLevelByName($map->getName());

            $this->degree += 1;
            if($this->degree > 360) $this->degree = 0;

            foreach ($game->getTeams() as $team) {
                if($team->hasFlag()) {
                    $cos = cos($this->degree) * 0.8;
                    $sin = sin($this->degree) * 0.8;

                    $y = [1];
                    foreach ($y as $value) {
                        $level->addParticle(new DustParticle($team->getSpawnLocation()->add($cos, $value, $sin), mt_rand(), mt_rand(), mt_rand(), mt_rand()));
                    }

                    if($currentTick % 20 === 0) {
                        foreach ($level->getPlayers() as $levelPlayer) {
                            $levelPlayer->playSound("firework.blast", 5.0, 1.0, [$levelPlayer]);
                            if(($fwPlayer = FlagWars::getPlayer($levelPlayer)) != null) {
                                if($fwPlayer->hasFlag())
                                    $level->addParticle(new FlameParticle($levelPlayer->asVector3()->add($cos, 0, $sin)));
                            }
                        }
                    }
                    break;
                }
            }

            foreach ($game->getKits() as $kit) {
                $kit->onUpdate($currentTick);
            }
        }
        if($currentTick % 20 !== 0) {
            return;
        }
        $this->points++;
        if($this->points > 3) {
            $this->points = 0;
        }
        $missingPlayers = Settings::$players_for_start - count($game->getPlayers());
        switch ($game->getState()) {
            case $game::STATE_WAITING: {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    if($player->isCreative()) continue;//For setup
                    $player->sendTip(LanguageProvider::getMessageContainer('wait-of-players-tip', $player->getName(), ["#needed" => $missingPlayers]) . str_repeat(".", $this->points));//todo: popup
                }
                if($missingPlayers > 0 && !$game->isForceStart()) {
                    break;
                }
                $game->setState($game::STATE_COUNTDOWN);
            }
            case $game::STATE_COUNTDOWN: {
                if(!$game->isForceStart()) {
                    if($missingPlayers > 0) {
                        $game->setState($game::STATE_WAITING);
                        break;
                    }
                } else {
                    if(count($game->getPlayers()) <= 1) {
                        $game->setState($game::STATE_WAITING);
                        $game->setForceStart(false);
                        break;
                    }
                }

                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $player->sendTip(LanguageProvider::getMessageContainer('countdown-tip', $player->getName(), ["#countdown" => $game->getCountdown()]) . str_repeat(".", $this->points));
                    if(in_array($game->getCountdown(), $this->showNumbers)) {
                        $player->sendTitle(TextFormat::AQUA.$game->getCountdown(), "");
                        $player->playSound("note.bass", 5.0, 2.0, [$player]);
                    }
                }
                if($game->getCountdown() === 5) $game->loadMap();
                $game->tickCountdown();
                if($game->getCountdown() <= 0) {
                    $game->setState($game::STATE_INGAME);
                }
                break;
            }
            case $game::STATE_INGAME: {
                $map = $game->getMap();
                $level = Server::getInstance()->getLevelByName($map->getName());
                $haveToWinFlags = Settings::$flag_to_win;
                if(count(array_filter($game->getTeams(), function (Team $team): bool {
                    return $team->isAlive();
                })) <= 1 || count(array_filter($game->getTeams(), function (Team $team) use ($haveToWinFlags): bool {
                    return $team->getFlagsSaved() >= $haveToWinFlags;
                })) >= 1) {
                    $game->setState($game::STATE_RESTART);
                    return;
                }

                if(!$game->isFlag()) {
                    if($game->getCountdown() === 0) {
                        $location = $map->getRandomFlagLocation();
                        $nbt = Entity::createBaseNBT($location);
                        $flag = new FlagEntity($level, $nbt);
                        $flag->spawnToAll();

                        $game->setFlag(true);
                        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                            FlagWarsProvider::createStrike($player, $location->asVector3());
                            $player->sendTitle(LanguageProvider::getMessageContainer("flag-spawn-title", $player->getName()), LanguageProvider::getMessageContainer("flag-spawn-subtitle", $player->getName(), ['#flaggsCount' => Settings::$flag_to_win]));
                        }
                    } elseif($game->getCountdown() <= 5) {
                        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                            $player->playSound("random.click", 5.0, 1.0, [$player]);
                            $player->sendTitle(TextFormat::AQUA.$game->getCountdown(), TextFormat::WHITE.LanguageProvider::getMessageContainer('flag-spawn-time-title', $player->getName()));
                        }
                    }
                    $game->tickCountdown();
                }

                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $fwPlayer = FlagWars::getPlayer($player);

                    Scoreboard::sendScoreboard($player, "§r§l§fFlagWars");
                    Scoreboard::setLine($player, 1);
                    $score = 2;
                    foreach ($game->getTeams() as $team) {
                        Scoreboard::setLine($player, $score, ($team->isPlayer($player) ? "§l" : "") . $team->getColor() . $team->getName() . "§r §7(" . $team->getFlagsSaved() . "/" . Settings::$flag_to_win . ")");
                        $score++;
                    }
                    Scoreboard::setLine($player, ++$score);
                    Scoreboard::setLine($player, ++$score, "§r§fKit:");
                    Scoreboard::setLine($player, ++$score, "§r§7" . (is_null($fwPlayer->getKit()) ? "???" : $fwPlayer->getKit()->getName()));
                    Scoreboard::setLine($player, ++$score);
                    Scoreboard::setLine($player, ++$score, "§r§fFlag:");
                    $flag = $game->getFlag();
                    Scoreboard::setLine($player, ++$score, "§r§7" . (
                        is_null($flag) ? "In " . ($game->getCountdown() + 1) . "s" :
                            (is_null($flag->getCarrier()) ? ($flag->hasBeenPickedUp() ? "§r§eDropped" : "§r§eSpawned") : ($tempTeam = FlagWars::getPlayer($flag->getCarrier())->getTeam())->getColor() . $tempTeam->getName())
                        )
                    );
                }
                break;
            }
            case $game::STATE_RESTART: {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $player->sendTip(FlagWars::PREFIX.TextFormat::RED.TextFormat::BOLD.$game->getCountdown() . str_repeat(".", $this->points));
                }
                $game->tickCountdown();
                if($game->getCountdown() === 3) {
                    foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                        CloudBridge::getCloudProvider()->dispatchProxyCommand($player->getName(), "hub");
                    }
                }
                if($game->getCountdown() === 0) {
                    $game->setState(-1);
                    Server::getInstance()->shutdown();
                }
                break;
            }
        }
    }
}