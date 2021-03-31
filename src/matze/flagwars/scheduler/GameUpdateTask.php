<?php

namespace matze\flagwars\scheduler;

use matze\flagwars\entity\FlagEntity;
use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\game\Team;
use matze\flagwars\utils\Scoreboard;
use matze\flagwars\utils\Settings;
use pocketmine\entity\Entity;
use pocketmine\level\particle\DustParticle;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class GameUpdateTask extends Task {

    /** @var int  */
    private $points = 0;

    /** @var int  */
    private $degree = 0;

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
                    $player->sendTip("Missing players: " . $missingPlayers . str_repeat(".", $this->points));//todo: popup
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
                    $player->sendTip("Time until start: " . $game->getCountdown() . str_repeat(".", $this->points));//todo: popup
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
                if(count(array_filter($game->getTeams(), function (Team $team): bool {
                    return $team->isAlive();
                })) <= 1 || count(array_filter($game->getTeams(), function (Team $team): bool {
                    return $team->getFlagsSaved() >= 3;
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
                            $player->sendTip("Flag spawned.");//todo: popup
                        }
                    } elseif($game->getCountdown() <= 5) {
                        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                            $player->sendTip("Flag spawns in: " . $game->getCountdown() . str_repeat(".", $this->points));//todo: popup
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
                    Scoreboard::setLine($player, ++$score, "§r§7" . (is_null($fwPlayer->getKit()) ? "N/A" : $fwPlayer->getKit()->getName()));
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
                    $player->sendTip("Server will be stopped: " . $game->getCountdown() . str_repeat(".", $this->points));//todo: popup
                }
                $game->tickCountdown();
                if($game->getCountdown() === 3) {
                    foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                        //CloudBridge::getCloudProvider()->dispatchProxyCommand($player->getName(), "hub");//todo
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