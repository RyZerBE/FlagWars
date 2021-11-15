<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\game\kits\Kit;
use ryzerbe\core\util\ItemUtils;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class CutterKit extends Kit {

    public function __construct()
    {
        $this->setDescription("Du kannst mit diesem Kit Wolle schneller abbauen. Ein wahrer Schneider!");
        parent::__construct();
    }

    /**
     * @param Player $player
     * @return array
     */
    public function getItems(Player $player): array {
        return [
            ItemUtils::addEnchantments(Item::get(ItemIds::SHEARS), [
                Enchantment::EFFICIENCY => 5
            ])
        ];
        #TODO: Nerf? Damage item? Maybe. #CONTENTS
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Schneider";
    }


    /**
     * @return int
     */
    public function getPrice(): int
    {
        return 80000;
    }
}