<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;

class InventoryTransactionListener implements Listener {

    /**
     * @param InventoryTransactionEvent $event
     */
    public function onTransaction(InventoryTransactionEvent $event): void {
        $player = $event->getTransaction()->getSource();
        $fwPlayer = FlagWars::getPlayer($player);
        $game = GameManager::getInstance();

        if($player->isCreative(true)) return;
        if(!$game->isIngame() || $fwPlayer->isSpectator()) {
            $event->setCancelled();
        }
    }
}