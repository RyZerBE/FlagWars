<?php

namespace matze\flagwars\listener;

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
        $game = GameManager::getInstance();

        if($player->isCreative(true)) return;
        if($game->getState() === GameManager::STATE_INGAME) return;

        $event->setCancelled();
    }
}