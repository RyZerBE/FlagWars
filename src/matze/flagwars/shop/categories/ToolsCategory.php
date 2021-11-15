<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use pocketmine\block\BlockIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class ToolsCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $shear = Item::get(ItemIds::SHEARS)->setCustomName(TextFormat::GOLD."Schere");
        $effi = Enchantment::getEnchantment(Enchantment::EFFICIENCY);

        $variable = Item::get(BlockIds::GLASS_PANE)->setCustomName("");
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