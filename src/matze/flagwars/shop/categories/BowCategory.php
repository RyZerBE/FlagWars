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

class BowCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $bow = Item::get(ItemIds::BOW)->setCustomName(TextFormat::YELLOW."Bow I");
        $bow2 = Item::get(ItemIds::BOW)->setCustomName(TextFormat::YELLOW."Bow II");
        $bow3 = Item::get(ItemIds::BOW)->setCustomName(TextFormat::YELLOW."Bow III");
        $arrow = Item::get(ItemIds::ARROW)->setCustomName(TextFormat::YELLOW."Arrow");

        $power = Enchantment::getEnchantment(Enchantment::POWER);
        $punch = Enchantment::getEnchantment(Enchantment::PUNCH);
        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);

        $bow2->addEnchantment(new EnchantmentInstance($unbreaking));
        $bow2->addEnchantment(new EnchantmentInstance($punch));
        $bow3->addEnchantment(new EnchantmentInstance($unbreaking));
        $bow3->addEnchantment(new EnchantmentInstance($power));
        $bow3->addEnchantment(new EnchantmentInstance($punch, 2));

        $bow->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Gold"]);
        $bow2->setLore([TextFormat::RED.TextFormat::BOLD.'7 '.TextFormat::YELLOW."Gold"]);
        $bow3->setLore([TextFormat::RED.TextFormat::BOLD.'13 '.TextFormat::YELLOW."Gold"]);
        $arrow->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Iron"]);


        $variable = Item::get(BlockIds::GLASS_PANE)->setCustomName("");
        for ($i = 9; $i < 27; $i++)
            $contents[$i] = $variable;


        $contents[19] = $bow;
        $contents[20] = $bow2;
        $contents[21] = $bow3;
        $contents[23] = $arrow;
        return $contents;
    }

    public function getName(): string
    {
        return "Bow";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Bow Category";
    }
}