<?php


namespace matze\flagwars\shop;


use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Color;
use pocketmine\utils\TextFormat;

class ShopManager
{

    public static function rm(Player $player, int $id = Item::BRICK, $count = 1)
    {
        $player->getInventory()->removeItem(Item::get($id, 0, $count));
    }

    public static function count(Player $player, int $id = Item::BRICK): int{
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
     * @return \pocketmine\utils\Color
     */
    public static function teamColorIntoColor(string $colorInt): Color
    {
        switch ($colorInt) {
            case TextFormat::RED:
                return new Color(152, 245, 255);
                break;
            case TextFormat::BLUE:
            case TextFormat::AQUA:
                return new Color(255, 0, 0);
                break;
            case TextFormat::YELLOW:
                return new Color(255,255,0);
                break;
            case TextFormat::GREEN:
            case TextFormat::DARK_GREEN:
                return new Color(127,255,0);
                break;
            case TextFormat::LIGHT_PURPLE:
                return new Color(255,105,180);
                break;
            case TextFormat::GOLD:
                return new Color(255,69,0);
                break;
            case TextFormat::DARK_PURPLE:
                return new Color(139,0,139);
                break;
        }

        return $color = new Color(255,240,245);
    }

    /**
     * @param string $colorInt
     * @return int
     */
    public static function teamColorIntoMeta(string $colorInt): int
    {
        switch ($colorInt) {
            case TextFormat::RED:
                return 14;
                break;
            case TextFormat::BLUE:
            case TextFormat::AQUA:
                return 11;
                break;
            case TextFormat::YELLOW:
                return 4;
                break;
            case TextFormat::GREEN:
            case TextFormat::DARK_GREEN:
                return 5;
                break;
            case TextFormat::LIGHT_PURPLE:
                return 6;
                break;
            case TextFormat::GOLD:
                return 1;
                break;
            case TextFormat::DARK_PURPLE:
                return 10;
                break;
        }

        return 0;
    }
}