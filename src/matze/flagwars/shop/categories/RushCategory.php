<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use matze\flagwars\shop\ShopManager;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class RushCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();

        $teamColor = ShopManager::teamColorIntoMeta($team->getColor());

        $sword = Item::get(Item::WOODEN_SWORD, 0, 1)->setCustomName(TextFormat::GOLD . "Holzschwert");
        $pickaxe = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::GOLD . "Schere");
        $blocks = Item::get(Item::WOOL, $teamColor, 32)->setCustomName(TextFormat::GOLD . "Wool");

        $sword->setLore([TextFormat::RED . TextFormat::BOLD . '10 ' . TextFormat::YELLOW . "Bronze"]);
        $pickaxe->setLore([TextFormat::RED . TextFormat::BOLD . '5 ' . TextFormat::YELLOW . "Bronze"]);
        $blocks->setLore([TextFormat::RED . TextFormat::BOLD . '8 ' . TextFormat::YELLOW . "Bronze"]);

        $knock = Enchantment::getEnchantment(Enchantment::KNOCKBACK);
        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);
        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);

        $eat = Item::get(Item::COOKED_PORKCHOP, 0, 2)->setCustomName(TextFormat::GOLD . "Bio-Schnitzel");

        $pickaxe->addEnchantment(new EnchantmentInstance($unbreaking));
        $sword->addEnchantment(new EnchantmentInstance($knock));
        $sword->addEnchantment(new EnchantmentInstance($unbreaking));

        $cap = Item::get(Item::LEATHER_CAP, 0, 1)->setCustomName(TextFormat::GOLD . "Helm");
        $hoodie = Item::get(Item::CHAIN_CHESTPLATE, 0, 1)->setCustomName(TextFormat::GOLD . "Brustplatte");
        $leggings = Item::get(Item::LEATHER_LEGGINGS, 0, 1)->setCustomName(TextFormat::GOLD . "Hose");
        $boots = Item::get(Item::LEATHER_BOOTS, 0, 1)->setCustomName(TextFormat::GOLD . "Schuhe");

        $cap->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);
        $eat->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);
        $hoodie->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Iron"]);
        $leggings->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);
        $boots->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);

        $cap->addEnchantment(new EnchantmentInstance($protection));
        $leggings->addEnchantment(new EnchantmentInstance($protection));
        $boots->addEnchantment(new EnchantmentInstance($protection));
        $hoodie->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 9; $i < 27; $i++)
            $contents[$i] = $variable;

        $contents[18] = $sword;
        $contents[19] = $pickaxe;
        $contents[20] = $blocks;
        $contents[21] = $eat;
        $contents[23] = $cap;
        $contents[24] = $hoodie;
        $contents[25] = $leggings;
        $contents[26] = $boots;

        return $contents;
    }

    public function getName(): string
    {
        return "Rush";
    }

    public function getCustomName(): string
    {
        return TextFormat::RED."REWE ".TextFormat::GRAY."- ".TextFormat::RED."Besser leben";
    }
}