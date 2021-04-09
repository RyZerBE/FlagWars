<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class SpecialCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $rp = Item::get(Item::BLAZE_ROD, 0, 1)->setCustomName(TextFormat::GOLD."Rettungsplattform");
        $ep = Item::get(Item::ENDER_PEARL, 0, 1)->setCustomName(TextFormat::GOLD."EnderPerle");
        $booster = Item::get(Item::GHAST_TEAR, 0, 1)->setCustomName(TextFormat::GOLD."Booster");

        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);

        $ep->addEnchantment(new EnchantmentInstance($unbreaking, 10));

        $rp->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Gold"]);
        $ep->setLore([TextFormat::RED.TextFormat::BOLD.'13 '.TextFormat::YELLOW."Gold"]);
        $booster->setLore([TextFormat::RED.TextFormat::BOLD.'7 '.TextFormat::YELLOW."Iron"]);

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");

        for ($i = 9; $i < 27; $i++)
            $contents[$i] = $variable;


        $contents[21] = $ep;
        $contents[22] = $rp;
        $contents[23] = $booster;

        return $contents;
    }

    public function getName(): string
    {
        return "Special";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Special Category";
    }
}