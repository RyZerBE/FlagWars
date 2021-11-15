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

class SpecialCategory extends ShopCategory
{

    public function getItems(?Team $team): array
    {
        $contents = $this->categoryList();
        $rp = Item::get(ItemIds::BLAZE_ROD)->setCustomName(TextFormat::GOLD."Rettungsplattform");
        $ep = Item::get(ItemIds::ENDER_PEARL)->setCustomName(TextFormat::GOLD."EnderPerle");
        $booster = Item::get(ItemIds::GHAST_TEAR)->setCustomName(TextFormat::GOLD."Booster");

        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);

        $ep->addEnchantment(new EnchantmentInstance($unbreaking, 10));

        $rp->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Gold"]);
        $ep->setLore([TextFormat::RED.TextFormat::BOLD.'13 '.TextFormat::YELLOW."Gold"]);
        $booster->setLore([TextFormat::RED.TextFormat::BOLD.'7 '.TextFormat::YELLOW."Iron"]);

        $variable = Item::get(BlockIds::GLASS_PANE)->setCustomName("");

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