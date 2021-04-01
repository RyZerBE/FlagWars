<?php

namespace matze\flagwars\listener;

use BauboLP\Core\Provider\LanguageProvider;
use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\ItemUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;

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
        if($game->getState() === GameManager::STATE_INGAME) return;

        if($fwPlayer === null) {
            $event->setCancelled();
            return;
        }

        if(ItemUtils::hasItemTag($item, "kit_item")) {
            $event->setCancelled();
            $player->sendMessage(FlagWars::PREFIX.LanguageProvider::getMessageContainer('cant-drop-kititem', $player->getName()));
            return;
        }

        $event->setCancelled();
    }
}