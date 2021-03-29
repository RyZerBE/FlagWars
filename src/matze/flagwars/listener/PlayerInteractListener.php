<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\utils\ItemUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class PlayerInteractListener implements Listener {

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $item = $event->getItem();
        $action = $event->getAction();
        $block = $event->getBlock();

        if(ItemUtils::hasItemTag($item, "function") && !$player->hasItemCooldown($item)) {
            $player->resetItemCooldown($item, 10);
            switch (ItemUtils::getItemTag($item, "function")) {
                case "kit_selection": {
                    break;
                }
                case "team_selection": {
                    break;
                }
                case "map_selection": {
                    break;
                }
            }
        }
    }
}