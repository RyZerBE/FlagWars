<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

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
            case $game::STATE_RESTART:
            case $game::STATE_WAITING: {
                if($fwPlayer->isSpectator()) {
                    break;
                }
            $fwPlayer->save();
            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayer->sendMessage(FlagWars::PREFIX.TextFormat::WHITE."[".TextFormat::RED."-".TextFormat::WHITE."] ".$player->getName().TextFormat::RESET.TextFormat::GRAY."(" . (count($game->getPlayers()) - 1) . "/" . $game->getMaxPlayers() . ")");//todo: message
                }
                break;
            }
            case $game::STATE_INGAME: {
                if($fwPlayer->isSpectator()) {
                    break;
                }
                $fwPlayer->save();
                if($fwPlayer->hasFlag()) {
                    $fwPlayer->setHasFlag(false);
                    $fwPlayer->getTeam()->setHasFlag(false);

                    foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                        $onlinePlayer->sendTip("Flag lost.");
                    }
                }
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayer->sendMessage(FlagWars::PREFIX.TextFormat::WHITE."[".TextFormat::RED."-".TextFormat::WHITE."] ".$player->getName().TextFormat::RESET.TextFormat::GRAY."(" . (count($game->getPlayers()) - 1) . "/" . $game->getMaxPlayers() . ")");//todo: message
                }
                break;
            }
        }
        $game->removePlayer($player);
        FlagWars::removePlayer($player);
    }
}