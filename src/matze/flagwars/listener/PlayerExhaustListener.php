<?php

namespace matze\flagwars\listener;

use matze\flagwars\game\GameManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

class PlayerExhaustListener implements Listener {

    /**
     * @param PlayerExhaustEvent $event
     */
    public function onExhaust(PlayerExhaustEvent $event): void {
        $player = $event->getPlayer();
        $game = GameManager::getInstance();

        $event->setAmount(0.025);
        if(!$game->isIngame()) {
            $player->setFood($player->getMaxFood());
        }
    }
}