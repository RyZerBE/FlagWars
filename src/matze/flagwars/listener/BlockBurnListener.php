<?php

namespace matze\flagwars\listener;

use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\Listener;

class BlockBurnListener implements Listener {

    /**
     * @param BlockBurnEvent $event
     */
    public function onBurn(BlockBurnEvent $event): void {
        $event->setCancelled();
    }
}