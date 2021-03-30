<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Server;

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

            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                $onlinePlayer->sendMessage($team->getColor() . "Team " . $team->getName() . "§r saved the flag.");//todo: message
            }
        }
    }
}