<?php


namespace matze\flagwars\shop;


use matze\flagwars\FlagWars;
use matze\flagwars\shop\listener\ShopTransactionListener;
use muqsit\invmenu\InvMenu;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShopCategorys
{
    
    public static function RushCategory(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");

        if(($fwPlayer = FlagWars::getPlayer($player)) != null)
            $teamColor = ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor());
        else
            $teamColor = 14;
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
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(18, $sword);
        $i->setItem(19, $pickaxe);
        $i->setItem(20, $blocks);
        $i->setItem(21, $eat);
        $i->setItem(23, $cap);
        $i->setItem(24, $hoodie);
        $i->setItem(25, $leggings);
        $i->setItem(26, $boots);

        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }

    public static function Blocks(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");

        if(($fwPlayer = FlagWars::getPlayer($player)) != null)
        $teamColor = ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor());
        else
            $teamColor = 14;
        $wool = Item::get(Item::WOOL, $teamColor, 32)->setCustomName(TextFormat::GOLD . "Wool");
        $clay = Item::get(Item::TERRACOTTA, $teamColor, 1)->setCustomName(TextFormat::GOLD . "Terracotta");

        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
        $block_category->addEnchantment(new EnchantmentInstance($protection));

        $wool->setLore([TextFormat::RED . TextFormat::BOLD . '8 ' . TextFormat::YELLOW . "Bronze"]);
        $clay->setLore([TextFormat::RED . TextFormat::BOLD . '12 ' . TextFormat::YELLOW . "Bronze"]);

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(24, $clay);
        $i->setItem(20, $wool);

        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }

    public static function Special(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");

        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");

        $rp = Item::get(Item::BLAZE_ROD, 0, 1)->setCustomName(TextFormat::GOLD."Rettungsplattform");
        $ep = Item::get(Item::ENDER_PEARL, 0, 1)->setCustomName(TextFormat::GOLD."EnderPerle");
        $booster = Item::get(Item::GHAST_TEAR, 0, 1)->setCustomName(TextFormat::GOLD."Booster");

        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);

        $ep->addEnchantment(new EnchantmentInstance($unbreaking, 10));

        $rp->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Gold"]);
        $ep->setLore([TextFormat::RED.TextFormat::BOLD.'13 '.TextFormat::YELLOW."Gold"]);
        $booster->setLore([TextFormat::RED.TextFormat::BOLD.'7 '.TextFormat::YELLOW."Iron"]);

        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
        $special_category->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(21, $rp);
        $i->setItem(22, $ep);
        $i->setItem(23, $booster);

        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }

    public static function Eat(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");


        $porkchop = Item::get(Item::COOKED_PORKCHOP, 0, 2)->setCustomName(TextFormat::GOLD."Bio-Schnitzel");
        $apple = Item::get(Item::APPLE, 0, 1)->setCustomName(TextFormat::GOLD."Apple");
        $cake = Item::get(Item::CAKE, 0, 1)->setCustomName(TextFormat::GOLD."Cake");
        $gold_apple = Item::get(Item::GOLDEN_APPLE, 0, 1)->setCustomName(TextFormat::GOLD."Gapple");

        $porkchop->setLore([TextFormat::RED.TextFormat::BOLD.'2 '.TextFormat::YELLOW."Bronze"]);
        $apple->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Bronze"]);
        $cake->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Iron"]);
        $gold_apple->setLore([TextFormat::RED.TextFormat::BOLD.'4 '.TextFormat::YELLOW."Iron"]);

        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
        $eat_category->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(20, $porkchop);
        $i->setItem(21, $apple);
        $i->setItem(23, $cake);
        $i->setItem(24, $gold_apple);
        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }

    public static function Bows(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");

        $bow = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::GOLD."Bow I");
        $bow2 = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::GOLD."Bow II");
        $bow3 = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::GOLD."Bow III");
        $arrow = Item::get(Item::ARROW, 0, 1)->setCustomName(TextFormat::GOLD."Arrow");

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

        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
        $bow_category->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(19, $bow);
        $i->setItem(20, $bow2);
        $i->setItem(21, $bow3);
        $i->setItem(23, $arrow);
        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }


    public static function Potions(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");


        $heal1 = Item::get(Item::POTION, 21, 1)->setCustomName(TextFormat::GOLD."Healing I");
        $heal2 = Item::get(Item::POTION, 22, 1)->setCustomName(TextFormat::GOLD."Healing II");
        $speed = Item::get(Item::POTION, 16, 1)->setCustomName(TextFormat::GOLD."Speed II");
        $strength = Item::get(Item::POTION, 31, 1)->setCustomName(TextFormat::GOLD."Strength I");

        $heal1->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Iron"]);
        $heal2->setLore([TextFormat::RED.TextFormat::BOLD.'6 '.TextFormat::YELLOW."Iron"]);
        $speed->setLore([TextFormat::RED.TextFormat::BOLD.'16 '.TextFormat::YELLOW."Iron"]);
        $strength->setLore([TextFormat::RED.TextFormat::BOLD.'7 '.TextFormat::YELLOW."Gold"]);

        $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
        $potion_category->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(20, $heal1);
        $i->setItem(21, $heal2);
        $i->setItem(23, $speed);
        $i->setItem(24, $strength);
        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }

    public static function Swords(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");

        $sword1 = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::GOLD."Sword I");
        $sword2 = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::GOLD."Sword II");
        $sword3 = Item::get(Item::IRON_SWORD, 0, 1)->setCustomName(TextFormat::GOLD."Sword III");
        $woodenSword = Item::get(Item::WOODEN_SWORD, 0, 1)->setCustomName(TextFormat::GOLD . "Holzschwert");

        $rod = Item::get(Item::FISHING_ROD, 0, 1)->setCustomName(TextFormat::YELLOW."Rod");

        $sword1->setLore([TextFormat::RED.TextFormat::BOLD.'1 '.TextFormat::YELLOW."Iron"]);
        $sword2->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Iron"]);
        $sword3->setLore([TextFormat::RED.TextFormat::BOLD.'5 '.TextFormat::YELLOW."Gold"]);
        $woodenSword->setLore([TextFormat::RED.TextFormat::BOLD.'10 '.TextFormat::YELLOW."Bronze"]);
        $rod->setLore([TextFormat::RED.TextFormat::BOLD.'5 '.TextFormat::YELLOW."Iron"]);

        $sharpness = Enchantment::getEnchantment(Enchantment::SHARPNESS);
        $knock = Enchantment::getEnchantment(Enchantment::KNOCKBACK);
        $unbreaking = Enchantment::getEnchantment(Enchantment::UNBREAKING);

        $woodenSword->addEnchantment(new EnchantmentInstance($unbreaking));
        $woodenSword->addEnchantment(new EnchantmentInstance($knock));
        $swords_category->addEnchantment(new EnchantmentInstance($unbreaking));

        $sword1->addEnchantment(new EnchantmentInstance($unbreaking));
        $sword2->addEnchantment(new EnchantmentInstance($unbreaking, 2));
        $sword2->addEnchantment(new EnchantmentInstance($sharpness, 1));
        $sword3->addEnchantment(new EnchantmentInstance($sharpness, 1));
        $sword3->addEnchantment(new EnchantmentInstance($unbreaking, 3));

        $rod->addEnchantment(new EnchantmentInstance($unbreaking));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(18, $woodenSword);
        $i->setItem(19, $sword1);
        $i->setItem(20, $sword2);
        $i->setItem(21, $sword3);
        $i->setItem(25, $rod);

        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }

    public static function Tools(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");


        $shear = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::GOLD."Schere");
        $effi = Enchantment::getEnchantment(Enchantment::EFFICIENCY);


        $pickaxe_category->addEnchantment(new EnchantmentInstance($effi));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $shear->setLore([TextFormat::RED.TextFormat::BOLD.'5 '.TextFormat::YELLOW."Bronze"]);
        $i->setItem(21, $shear);

        $shear->setLore([TextFormat::RED.TextFormat::BOLD.'3 '.TextFormat::YELLOW."Iron"]);
        $shear->addEnchantment(new EnchantmentInstance($effi));
        $i->setItem(22, $shear);

        $shear->setLore([TextFormat::RED.TextFormat::BOLD.'2 '.TextFormat::YELLOW."Gold"]);
        $shear->addEnchantment(new EnchantmentInstance($effi, 2));
        $shear->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
        $i->setItem(23, $shear);

        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }

    public static function Protection(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->readonly();
        $menu->setName("");


        $block_category = Item::get(Item::TERRACOTTA, 0, 1)->setCustomName(TextFormat::AQUA . "Blocks");
        $swords_category = Item::get(Item::GOLDEN_SWORD, 0, 1)->setCustomName(TextFormat::AQUA . "Swords");
        $pickaxe_category = Item::get(Item::SHEARS, 0, 1)->setCustomName(TextFormat::AQUA . "Tools");
        $protect_category = Item::get(Item::DIAMOND_LEGGINGS, 0, 1)->setCustomName(TextFormat::AQUA . "Protection");
        $special_category = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1)->setCustomName(TextFormat::AQUA . "Special");
        $potion_category = Item::get(Item::POTION, 8, 1)->setCustomName(TextFormat::AQUA . "Potions");
        $bow_category = Item::get(Item::BOW, 0, 1)->setCustomName(TextFormat::AQUA . "Bows");
        $eat_category = Item::get(Item::COOKED_CHICKEN, 0, 1)->setCustomName(TextFormat::AQUA . "Eat");

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
        $protect_category->addEnchantment(new EnchantmentInstance($protection));

        $variable = Item::get(Item::GLASS_PANE, 0, 1)->setCustomName("");
        for ($i = 0; $i < 27; $i++)
            $menu->getInventory()->setItem($i, $variable);


        $i = $menu->getInventory();
        $i->setItem(0, $block_category);
        $i->setItem(1, $swords_category);
        $i->setItem(2, $pickaxe_category);
        $i->setItem(3, $protect_category);
        $i->setItem(5, $potion_category);
        $i->setItem(6, $eat_category);
        $i->setItem(7, $bow_category);
        $i->setItem(8, $special_category);

        $i->setItem(18, $boots);
        $i->setItem(19, $leggings);
        $i->setItem(20, $cap);
        $i->setItem(21, $hoodie1);
        $i->setItem(23, $hoodie2);
        $i->setItem(24, $hoodie3);
        $i->setItem(26, $fall_boots);

        $menu->send($player);
        $menu->setListener([new ShopTransactionListener(), "onTransaction"]);
    }
}