<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\game\kits\Kit;
use matze\flagwars\utils\ItemUtils;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\Player;

class CutterKit extends Kit {

    /**
     * @param Player $player
     * @return array
     */
    public function getItems(Player $player): array {
        return [
            ItemUtils::addEnchantments(Item::get(Item::SHEARS), [
                Enchantment::EFFICIENCY => 5
            ])
        ];
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Schneider";
    }
}