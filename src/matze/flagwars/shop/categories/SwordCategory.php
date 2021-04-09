<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class SwordCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();

        $sword1 = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::GOLD . "Sword I");
        $sword2 = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::GOLD . "Sword II");
        $sword3 = Item::get(Item::IRON_SWORD, 0, 1)->setCustomName(TextFormat::GOLD . "Sword III");
        $woodenSword = Item::get(Item::WOODEN_SWORD, 0, 1)->setCustomName(TextFormat::GOLD . "Holzschwert");

        $rod = Item::get(Item::FISHING_ROD, 0, 1)->setCustomName(TextFormat::YELLOW . "Rod");

        $sword1->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Iron"]);
        $sword2->setLore([TextFormat::RED . TextFormat::BOLD . '3 ' . TextFormat::YELLOW . "Iron"]);
        $sword3->setLore([TextFormat::RED . TextFormat::BOLD . '5 ' . TextFormat::YELLOW . "Gold"]);
        $woodenSword->setLore([TextFormat::RED . TextFormat::BOLD . '10 ' . TextFormat::YELLOW . "Bronze"]);
        $rod->setLore([TextFormat::RED . TextFormat::BOLD . '5 ' . TextFormat::YELLOW . "Iron"]);

        $sharpness = Enchantment::getEnchantment(Enchantment::SHARPNESS);
        $knock = Enchantment::getEnchantment(Enchantment::KNOCKBACK);
        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);

        $woodenSword->addEnchantment(new EnchantmentInstance($unbreaking));
        $woodenSword->addEnchantment(new EnchantmentInstance($knock));

        $sword1->addEnchantment(new EnchantmentInstance($unbreaking));
        $sword2->addEnchantment(new EnchantmentInstance($unbreaking, 2));
        $sword2->addEnchantment(new EnchantmentInstance($sharpness, 1));
        $sword3->addEnchantment(new EnchantmentInstance($sharpness, 1));
        $sword3->addEnchantment(new EnchantmentInstance($unbreaking, 3));

        $rod->addEnchantment(new EnchantmentInstance($unbreaking));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 9; $i < 27; $i++) {
            $contents[$i] = $variable;
        }

        $contents[18] = $woodenSword;
        $contents[19] = $sword2;
        $contents[20] = $sword3;
        $contents[25] = $rod;

        return $contents;
    }

    public function getName(): string
    {
        return "Sword";
    }

    public function getCustomName(): string
    {
        return TextFormat::DARK_AQUA."Sword Category";
    }
}