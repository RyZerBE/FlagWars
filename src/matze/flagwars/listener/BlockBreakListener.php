<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\Settings;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class BlockBreakListener implements Listener {

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event): void {
        $game = GameManager::getInstance();
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $block = $event->getBlock();
        $item = $event->getItem();

        if($player->isCreative(true)) return;
        if(!$game->isIngame()) {
            $event->setCancelled();
            return;
        }
        if(in_array($block->getId(), Settings::$breakableBlocks)) {
            return;
        }
        if(!$game->isBlock($block)) {
            $event->setCancelled();
            return;
        }
        $game->removeBlock($block);
    }
}