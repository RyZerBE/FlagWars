<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class PlayerJoinListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $game = GameManager::getInstance();

        $fwPlayer->reset();
        $event->setJoinMessage("");
        switch ($game->getState()) {
            case $game::STATE_COUNTDOWN: {}
            case $game::STATE_WAITING: {
                $player->setGamemode(2);
                $player->teleport(Settings::$waiting_lobby_location);
                if(count($game->getPlayers()) >= $game->getMaxPlayers()) {
                    return;
                }

                $fwPlayer->getLobbyItems();
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayer->sendMessage($player->getName() . " joined. (" . count($game->getPlayers()) - 1 . "/" . $game->getMaxPlayers() . ")");//todo: message
                }
                break;
            }
            case $game::STATE_INGAME: {
                $rdmPlayer = $game->getPlayers()[array_rand($game->getPlayers())];
                $player->teleport($rdmPlayer);
                break;
            }
            case $game::STATE_RESTART: {
                Server::getInstance()->dispatchCommand($player, "hub");
                break;
            }
        }
    }
}