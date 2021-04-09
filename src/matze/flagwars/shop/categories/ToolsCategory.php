<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class ToolsCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $shear = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::GOLD."Schere");
        $effi = Enchantment::getEnchantment(Enchantment::EFFICIENCY);

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 9; $i < 27; $i++)
            $contents[$i] = $variable;


        $shear->setLore([TextFormat::RED.TextFormat::BOLD.'5 '.TextFormat::YELLOW."Bronze"]);
        $contents[21] = $shear;

        $shear->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Iron"]);
        $shear->addEnchantment(new EnchantmentInstance($effi));
        $contents[22] = $shear;

        $shear->setLore([TextFormat::RED.TextFormat::BOLD.'2 '.TextFormat::YELLOW."Gold"]);
        $shear->addEnchantment(new EnchantmentInstance($effi, 2));
        $shear->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
        $contents[23] = $shear;

        return $contents;
    }

    public function getName(): string
    {
        return "Tools";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Tool Category";
    }
}