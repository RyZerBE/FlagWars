<?php


namespace matze\flagwars\shop;


use matze\flagwars\shop\categories\BlockCategory;
use matze\flagwars\shop\categories\BowCategory;
use matze\flagwars\shop\categories\EatCategory;
use matze\flagwars\shop\categories\PotionCategory;
use matze\flagwars\shop\categories\ProtectionCategory;
use matze\flagwars\shop\categories\RushCategory;
use matze\flagwars\shop\categories\SpecialCategory;
use matze\flagwars\shop\categories\SwordCategory;
use matze\flagwars\shop\categories\ToolsCategory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\utils\Color;
use pocketmine\utils\TextFormat;

class ShopManager
{
    /** @var ShopCategory[] */
    public static array $categories = [];

    public static function loadCategories(): void
    {
        $categories = [
            new BlockCategory(),
            new BowCategory(),
            new EatCategory(),
            new PotionCategory(),
            new ProtectionCategory(),
            new RushCategory(),
            new SpecialCategory(),
            new SwordCategory(),
            new ToolsCategory()
        ];
        
        /** @var ShopCategory $category */
        foreach ($categories as $category)
            self::$categories[$category->getName()] = $category;
    }

    public static function rm(Player $player, int $id = ItemIds::BRICK, $count = 1)
    {
        $player->getInventory()->removeItem(Item::get($id, 0, $count));
    }

    public static function count(Player $player, int $id = ItemIds::BRICK): int{
        $all = 0;
        $inv = $player->getInventory();
        $content = $inv->getContents();
        foreach ($content as $item) {
            if ($item->getId() == $id) {
                $c = $item->count;
                $all = $all + $c;
            }
        }
        return $all;
    }


    public static function addItem(Player $player, $id, $count, $name) {
        $item = Item::get($id, 0, $count)->setCustomName($name);
        $player->getInventory()->addItem($item);
    }

    public static function setPrice(Player $player, int $price, int $id) : bool {
        $resCount = self::count($player, $id);
        if($resCount < $price) {
            return false;
        } else {
            self::rm($player, $id, $price);
            return true;
        }
    }

    /**
     * @param string $colorInt
     * @return Color
     */
    public static function teamColorIntoColor(string $colorInt): Color {
        return match ($colorInt) {
            TextFormat::RED => new Color(152, 245, 255),
            TextFormat::BLUE, TextFormat::AQUA => new Color(255, 0, 0),
            TextFormat::YELLOW => new Color(255, 255, 0),
            TextFormat::GREEN, TextFormat::DARK_GREEN => new Color(127, 255, 0),
            TextFormat::LIGHT_PURPLE => new Color(255, 105, 180),
            TextFormat::GOLD => new Color(255, 69, 0),
            TextFormat::DARK_PURPLE => new Color(139, 0, 139),
            default => $color = new Color(255, 240, 245),
        };
    }

    /**
     * @param string $colorInt
     * @return int
     */
    public static function teamColorIntoMeta(string $colorInt): int {
        return match ($colorInt) {
            TextFormat::RED => 14,
            TextFormat::BLUE, TextFormat::AQUA => 11,
            TextFormat::YELLOW => 4,
            TextFormat::GREEN, TextFormat::DARK_GREEN => 5,
            TextFormat::LIGHT_PURPLE => 6,
            TextFormat::GOLD => 1,
            TextFormat::DARK_PURPLE => 10,
            default => 0,
        };
    }
}