<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use matze\flagwars\shop\ShopManager;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class BlockCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $teamColor = ShopManager::teamColorIntoMeta($team->getColor());

        $wool = Item::get(Item::WOOL, $teamColor, 32)->setCustomName(TextFormat::GOLD . "Wool");
        $clay = Item::get(Item::TERRACOTTA, $teamColor, 1)->setCustomName(TextFormat::GOLD . "Terracotta");
        $glass = Item::get(Item::STAINED_GLASS, $teamColor, 2)->setCustomName(TextFormat::GOLD . "Glass");

        $wool->setLore([TextFormat::RED . TextFormat::BOLD . '8 ' . TextFormat::YELLOW . "Bronze"]);
        $clay->setLore([TextFormat::RED . TextFormat::BOLD . '12 ' . TextFormat::YELLOW . "Bronze"]);

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 9; $i < 27; $i++)
            $contents[$i] = $variable;


        $contents[20] = $wool;
        $contents[22] = $glass;
        $contents[24] = $clay;
        return $contents;
    }

    public function getName(): string
    {
        return "Block";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Block Category";
    }
}