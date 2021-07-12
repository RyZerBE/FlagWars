<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;

class PlayerLoginListener implements Listener {

    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event): void {
        $player = $event->getPlayer();
        FlagWars::addPlayer($player);

        //todo: register
    }
}