<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class PotionCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $heal1 = Item::get(ItemIds::POTION, 21)->setCustomName(TextFormat::YELLOW."Healing I");
        $heal2 = Item::get(ItemIds::POTION, 22)->setCustomName(TextFormat::YELLOW."Healing II");
        $speed = Item::get(ItemIds::POTION, 16)->setCustomName(TextFormat::YELLOW."Speed II");
        $strength = Item::get(ItemIds::POTION, 31)->setCustomName(TextFormat::YELLOW."Strength I");

        $heal1->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Iron"]);
        $heal2->setLore([TextFormat::RED.TextFormat::BOLD.'6 '.TextFormat::YELLOW."Iron"]);
        $speed->setLore([TextFormat::RED.TextFormat::BOLD.'16 '.TextFormat::YELLOW."Iron"]);
        $strength->setLore([TextFormat::RED.TextFormat::BOLD.'7 '.TextFormat::YELLOW."Gold"]);

        $variable = Item::get(BlockIds::GLASS_PANE)->setCustomName("");
        for ($i = 9; $i < 27; $i++)
            $contents[$i] = $variable;


        $contents[20] = $heal1;
        $contents[21] = $heal2;
        $contents[23] = $speed;
        $contents[24] = $strength;
        return $contents;
    }

    public function getName(): string
    {
        return "Potion";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Potion Category";
    }
}