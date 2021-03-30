<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\FlagWars;
use matze\flagwars\game\kits\Kit;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;

class StarterKit extends Kit {

    /**
     * @param Player $player
     * @return array
     */
    public function getItems(Player $player): array {
        return [
            Item::get(Item::LEATHER_CHESTPLATE),
            Item::get(Item::SHEARS),
            ItemFactory::get(Item::WOOL, FlagWars::getPlayer($player)->getTeam()->getBlockMeta(), 32),
            ItemFactory::get(Item::STEAK, 0, 16),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Starter";
    }

    /**
     * @return bool
     */
    public function getItemsOnRespawn(): bool {
        return false;
    }
}