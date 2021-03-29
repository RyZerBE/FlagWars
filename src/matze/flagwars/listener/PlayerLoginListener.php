<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\provider\FlagWarsProvider;
use matze\flagwars\utils\AsyncExecuter;
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