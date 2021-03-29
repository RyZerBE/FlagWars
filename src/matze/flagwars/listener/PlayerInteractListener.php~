<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\forms\Forms;
use matze\flagwars\game\GameManager;
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
        $game = GameManager::getInstance();

        if(ItemUtils::hasItemTag($item, "function") && !$player->hasItemCooldown($item)) {
            $player->resetItemCooldown($item, 10);
            $event->setCancelled();
            switch (ItemUtils::getItemTag($item, "function")) {
                case "kit_selection": {
                    break;
                }
                case "team_selection": {
                    Forms::getSelectTeamForm()->open($player);
                    break;
                }
                case "map_selection": {
                    Forms::getSelectMapForm()->open($player);
                    break;
                }
            }
        }
    }
}