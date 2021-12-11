<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\Loader;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Server;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\statssystem\provider\StatsAsyncProvider;

class PlayerMoveListener implements Listener {

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event): void {
        $game = GameManager::getInstance();
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);

        if($fwPlayer->isSpectator()) return;
        if(!$game->isIngame()) {
            return;
        }

        $team = $fwPlayer->getTeam();
        if($fwPlayer->hasFlag() && $player->distance($team->getSpawnLocation()) <= 0.8) {
            $fwPlayer->setHasFlag(false);
            $team->setHasFlag(false);
            $team->addFlagsSaved();
            $game->setFlag(false);
            $player->playSound("random.levelup", 5.0, 1.0, [$player]);

            foreach($team->getPlayers() as $teamPlayer) {
                CoinProvider::addCoins($teamPlayer->getName(), 50);
                RyZerPlayerProvider::getRyzerPlayer($teamPlayer->getName())->getNetworkLevel()->addProgress(25);
            }

            StatsAsyncProvider::appendStatistic($player->getName(), Loader::STATS_CATEGORY, "flags", 1);
            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer)
                $onlinePlayer->sendMessage(FlagWars::PREFIX.LanguageProvider::getMessageContainer('fw-flag-saved', $onlinePlayer->getName(), ["#team" => $team->getColor()."Team ".$team->getName()]));
        }
    }
}