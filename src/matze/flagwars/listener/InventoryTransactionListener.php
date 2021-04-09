<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;

class InventoryTransactionListener implements Listener {

    /**
     * @param InventoryTransactionEvent $event
     * @priority MONITOR
     */
    public function onTransaction(InventoryTransactionEvent $event): void {
        $player = $event->getTransaction()->getSource();
        $fwPlayer = FlagWars::getPlayer($player);
        $game = GameManager::getInstance();

        if($player->isCreative(true)) return;
        if(!$game->isIngame() || $fwPlayer->isSpectator()) {
            $event->setCancelled();
            return;
        }

        #InvMenu Fix
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();
        foreach ($transaction->getActions() as $action) {
            if ($action instanceof SlotChangeAction && $event->isCancelled()) $action->getInventory()->sendSlot($action->getSlot(), $player);
        }
    }
}