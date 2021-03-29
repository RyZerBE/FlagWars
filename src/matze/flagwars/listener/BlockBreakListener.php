<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\ItemUtils;
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

        /*
        if(ItemUtils::hasItemTag($item, "map_setup")) {
            $map = $game->getMapByName($player->getLevel()->getFolderName());
            if(!is_null($map)) {
                $settings = $map->getSettings();
                switch (ItemUtils::getItemTag($item, "map_setup")) {
                    case "": {
                        break;
                    }
                }
            }
        }*/
    }
}