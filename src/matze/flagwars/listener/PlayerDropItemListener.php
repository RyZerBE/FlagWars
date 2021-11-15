<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use ryzerbe\core\util\ItemUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use ryzerbe\core\language\LanguageProvider;

class PlayerDropItemListener implements Listener {

    /**
     * @param PlayerDropItemEvent $event
     */
    public function onDrop(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        $game = GameManager::getInstance();
        $fwPlayer = FlagWars::getPlayer($player);


        if($player->isCreative(true)) return;
        if($fwPlayer->isSpectator()) {
            $event->setCancelled();
            return;
        }
        if($game->isIngame()) {
            if(ItemUtils::hasItemTag($item, "kit_item")) {
                $event->setCancelled();
                $player->sendMessage(FlagWars::PREFIX . LanguageProvider::getMessageContainer('cant-drop-kititem', $player->getName()));
            }
            return;
        }
        $event->setCancelled();
    }
}