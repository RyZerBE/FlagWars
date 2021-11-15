<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\FlagWars;
use matze\flagwars\game\kits\Kit;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class StarterKit extends Kit {

    public function __construct()
    {
        $this->setDescription("Mit diesem Kit kannst Du richtig durchstarten. Gönn dir einfach Starter-Equipment");
        parent::__construct();
    }

    /**
     * @param Player $player
     * @return array
     */
    public function getItems(Player $player): array {
        return [
            Item::get(ItemIds::LEATHER_CHESTPLATE),
            Item::get(ItemIds::SHEARS),
            ItemFactory::get(BlockIds::WOOL, FlagWars::getPlayer($player)->getTeam()->getBlockMeta(), 32),
            ItemFactory::get(ItemIds::STEAK, 0, 16),
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

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return 10000;
    }
}