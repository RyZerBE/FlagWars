<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\event\player\RyZerPlayerAuthEvent;
use ryzerbe\core\util\ItemUtils;

class PlayerJoinListener implements Listener {

    /**
     * @param RyZerPlayerAuthEvent $event
     */
    public function onAuth(RyZerPlayerAuthEvent $event): void {
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $game = GameManager::getInstance();

        $fwPlayer->reset();
        switch ($game->getState()) {
            case $game::STATE_COUNTDOWN: {}
            case $game::STATE_WAITING: {
                $player->setGamemode(2);
                $fwPlayer->load();
                $player->teleport(Settings::$waiting_lobby_location);
                if(count($game->getPlayers()) >= $game->getMaxPlayers()) {
                    return;
                }
                $game->addPlayer($player);
                $fwPlayer->getLobbyItems();
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayer->sendMessage(FlagWars::PREFIX.TextFormat::WHITE."[".TextFormat::GREEN."+".TextFormat::WHITE."] ".$player->getDisplayName().TextFormat::RESET.TextFormat::GRAY."(" . count($game->getPlayers()) . "/" . $game->getMaxPlayers() . ")");//todo: message
                }
                break;
            }
            case $game::STATE_INGAME: {
                $player->setGamemode(3);
                $player->teleport($game->getMap()->getSpectatorLocation());
                $item = ItemUtils::addItemTag(Item::get(Item::COMPASS)->setCustomName(TextFormat::GOLD."Teleporter"), "player_teleporter", "function");
                $player->getInventory()->setItem(4, $item);
                break;
            }
            case $game::STATE_RESTART: {
                //CloudBridge::getCloudProvider()->dispatchProxyCommand($player->getName(), "hub");//todo
                break;
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event){
        $event->setJoinMessage("");
    }
}