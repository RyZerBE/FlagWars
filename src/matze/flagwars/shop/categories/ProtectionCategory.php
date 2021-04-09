<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class ProtectionCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);
        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
        $fallprotection = Enchantment::getEnchantment(Enchantment::FEATHER_FALLING);


        $cap = Item::get(Item::LEATHER_CAP, 0, 1)->setCustomName(TextFormat::YELLOW."Cap");
        $hoodie1 = Item::get(Item::CHAIN_CHESTPLATE, 0, 1)->setCustomName(TextFormat::YELLOW."Hoodie I");
        $leggings = Item::get(Item::LEATHER_LEGGINGS, 0, 1)->setCustomName(TextFormat::YELLOW."Leggings");
        $boots = Item::get(Item::LEATHER_BOOTS, 0, 1)->setCustomName(TextFormat::YELLOW."Boots");
        $hoodie2 = Item::get(Item::CHAINMAIL_CHESTPLATE, 0, 1)->setCustomName(TextFormat::YELLOW."Hoodie II");
        $hoodie3 = Item::get(Item::CHAINMAIL_CHESTPLATE, 0, 1)->setCustomName(TextFormat::YELLOW."Hoodie III");
        $fall_boots = Item::get(Item::GOLD_BOOTS, 0, 1)->setCustomName(TextFormat::YELLOW."Fall Boots");

        $cap->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Bronze"]);
        $hoodie1->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Iron"]);
        $hoodie2->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Iron"]);
        $hoodie3->setLore([TextFormat::RED.TextFormat::BOLD.'7 '.TextFormat::YELLOW."Iron"]);
        $fall_boots->setLore([TextFormat::RED.TextFormat::BOLD.'12 '.TextFormat::YELLOW."Iron"]);
        $leggings->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Bronze"]);
        $boots->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Bronze"]);

        $hoodie1->addEnchantment(new EnchantmentInstance($protection));
        $hoodie2->addEnchantment(new EnchantmentInstance($protection, 2));
        $hoodie2->addEnchantment(new EnchantmentInstance($unbreaking, 2));
        $hoodie3->addEnchantment(new EnchantmentInstance($unbreaking, 2));
        $hoodie3->addEnchantment(new EnchantmentInstance($protection, 3));
        $fall_boots->addEnchantment(new EnchantmentInstance($fallprotection, 3));

        $cap->addEnchantment(new EnchantmentInstance($protection));
        $leggings->addEnchantment(new EnchantmentInstance($protection));
        $boots->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i =9; $i < 27; $i++)
            $contents[$i] = $variable;


        $contents[18] = $boots;
        $contents[19] = $leggings;
        $contents[20] = $cap;
        $contents[21] = $hoodie1;
        $contents[23] = $hoodie2;
        $contents[24] = $hoodie3;
        $contents[26] = $fall_boots;
        return $contents;
    }

    public function getName(): string
    {
        return "Protection";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Protection Category";
    }
}