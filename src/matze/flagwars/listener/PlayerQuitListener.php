<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class PlayerQuitListener implements Listener {

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $game = GameManager::getInstance();


        $event->setQuitMessage("");
        switch ($game->getState()) {
            case $game::STATE_COUNTDOWN: {}
            case $game::STATE_WAITING: {
                if($fwPlayer->isSpectator()) {
                    break;
                }
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayer->sendMessage($player->getName() . " left. (" . count($game->getPlayers()) - 1 . "/" . $game->getMaxPlayers() . ")");//todo: message
                }
                break;
            }
            case $game::STATE_INGAME: {}
            case $game::STATE_RESTART: {
                if($fwPlayer->isSpectator()) {
                    break;
                }
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayer->sendMessage($player->getName() . " left.");//todo: message
                }
                break;
            }
        }
        FlagWars::removePlayer($player);
    }
}