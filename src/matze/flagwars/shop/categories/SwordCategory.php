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

class SwordCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();

        $sword1 = Item::get(ItemIds::GOLDEN_SWORD)->setCustomName(TextFormat::GOLD . "Sword I");
        $sword2 = Item::get(ItemIds::GOLDEN_SWORD)->setCustomName(TextFormat::GOLD . "Sword II");
        $sword3 = Item::get(ItemIds::IRON_SWORD)->setCustomName(TextFormat::GOLD . "Sword III");
        $woodenSword = Item::get(ItemIds::WOODEN_SWORD)->setCustomName(TextFormat::GOLD . "Holzschwert");

        $rod = Item::get(ItemIds::FISHING_ROD)->setCustomName(TextFormat::YELLOW . "Rod");

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

        $variable = Item::get(BlockIds::GLASS_PANE)->setCustomName("");
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