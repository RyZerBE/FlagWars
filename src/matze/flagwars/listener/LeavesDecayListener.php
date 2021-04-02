<?php

namespace matze\flagwars\listener;

use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\Listener;

class LeavesDecayListener implements Listener {

    /**
     * @param LeavesDecayEvent $event
     */
    public function onDecay(LeavesDecayEvent $event): void {
        $event->setCancelled();
    }
}