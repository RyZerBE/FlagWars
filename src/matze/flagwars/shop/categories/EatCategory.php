<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class EatCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents  = $this->categoryList();
        $porkchop = Item::get(ItemIds::COOKED_PORKCHOP, 0, 2)->setCustomName(TextFormat::YELLOW."Schnitzel");
        $apple = Item::get(ItemIds::APPLE)->setCustomName(TextFormat::YELLOW."Apple");
        $cake = Item::get(ItemIds::CAKE)->setCustomName(TextFormat::YELLOW."Cake");
        $gold_apple = Item::get(ItemIds::GOLDEN_APPLE)->setCustomName(TextFormat::YELLOW."Gapple");

        $porkchop->setLore([TextFormat::RED.TextFormat::BOLD.'2 '.TextFormat::YELLOW."Bronze"]);
        $apple->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Bronze"]);
        $cake->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Iron"]);
        $gold_apple->setLore([TextFormat::RED.TextFormat::BOLD.'4 '.TextFormat::YELLOW."Iron"]);

        $variable = Item::get(BlockIds::GLASS_PANE)->setCustomName("");
        for ($i = 9; $i < 27; $i++)
            $contents[$i] = $variable;

        $contents[20] = $porkchop;
        $contents[21] = $apple;
        $contents[23] = $cake;
        $contents[24] = $gold_apple;
        return $contents;
    }

    public function getName(): string
    {
        return "Eat";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Eat Category";
    }
}