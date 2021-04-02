<?php

namespace matze\flagwars\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;

class PlayerBedEnterListener implements Listener {

    /**
     * @param PlayerBedEnterEvent $event
     */
    public function onEnter(PlayerBedEnterEvent $event): void {
        $event->setCancelled();
    }
}