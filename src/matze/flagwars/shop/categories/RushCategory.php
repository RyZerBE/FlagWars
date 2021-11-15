<?php


namespace matze\flagwars\shop\categories;


use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopCategory;
use matze\flagwars\shop\ShopManager;
use pocketmine\block\BlockIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class RushCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();

        $teamColor = ShopManager::teamColorIntoMeta($team->getColor());

        $sword = Item::get(ItemIds::WOODEN_SWORD)->setCustomName(TextFormat::GOLD . "Holzschwert");
        $pickaxe = Item::get(ItemIds::SHEARS)->setCustomName(TextFormat::GOLD . "Schere");
        $blocks = Item::get(BlockIds::WOOL, $teamColor, 32)->setCustomName(TextFormat::GOLD . "Wool");

        $sword->setLore([TextFormat::RED . TextFormat::BOLD . '10 ' . TextFormat::YELLOW . "Bronze"]);
        $pickaxe->setLore([TextFormat::RED . TextFormat::BOLD . '5 ' . TextFormat::YELLOW . "Bronze"]);
        $blocks->setLore([TextFormat::RED . TextFormat::BOLD . '8 ' . TextFormat::YELLOW . "Bronze"]);

        $knock = Enchantment::getEnchantment(Enchantment::KNOCKBACK);
        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);
        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);

        $eat = Item::get(ItemIds::COOKED_PORKCHOP, 0, 2)->setCustomName(TextFormat::GOLD . "Bio-Schnitzel");

        $pickaxe->addEnchantment(new EnchantmentInstance($unbreaking));
        $sword->addEnchantment(new EnchantmentInstance($knock));
        $sword->addEnchantment(new EnchantmentInstance($unbreaking));

        $cap = Item::get(ItemIds::LEATHER_CAP)->setCustomName(TextFormat::GOLD . "Helm");
        $hoodie = Item::get(ItemIds::CHAIN_CHESTPLATE)->setCustomName(TextFormat::GOLD . "Brustplatte");
        $leggings = Item::get(ItemIds::LEATHER_LEGGINGS)->setCustomName(TextFormat::GOLD . "Hose");
        $boots = Item::get(ItemIds::LEATHER_BOOTS)->setCustomName(TextFormat::GOLD . "Schuhe");

        $cap->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);
        $eat->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);
        $hoodie->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Iron"]);
        $leggings->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);
        $boots->setLore([TextFormat::RED . TextFormat::BOLD . '1 ' . TextFormat::YELLOW . "Bronze"]);

        $cap->addEnchantment(new EnchantmentInstance($protection));
        $leggings->addEnchantment(new EnchantmentInstance($protection));
        $boots->addEnchantment(new EnchantmentInstance($protection));
        $hoodie->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(BlockIds::GLASS_PANE)->setCustomName("");
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