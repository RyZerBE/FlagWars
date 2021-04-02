<?php

namespace matze\flagwars\listener;

use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;

class CraftItemListener implements Listener {

    /**
     * @param CraftItemEvent $event
     */
    public function onCraft(CraftItemEvent $event): void {
        $event->setCancelled();
    }
}