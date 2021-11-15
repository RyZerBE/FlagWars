<?php

namespace matze\flagwars\listener;

use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\Listener;

class BlockFormListener implements Listener {

    /**
     * @param BlockFormEvent $event
     */
    public function onForm(BlockFormEvent $event): void {
        $allowedBlocks = [
            BlockIds::WATER
        ];
        $block = $event->getBlock();
        if(!in_array($block->getId(), $allowedBlocks)) {
            $event->setCancelled();
        }
    }
}