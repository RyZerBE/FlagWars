<?php


namespace matze\flagwars\shop\listener;


use matze\flagwars\FlagWars;
use matze\flagwars\shop\ShopCategorys;
use matze\flagwars\shop\ShopManager;
use pocketmine\item\Item;
use pocketmine\item\LeatherBoots;
use pocketmine\item\LeatherCap;
use pocketmine\item\LeatherPants;
use pocketmine\item\LeatherTunic;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShopTransactionListener
{

    /**
     * @param \pocketmine\Player $player
     * @param \pocketmine\item\Item $clickedItem
     */
    public function onTransaction(Player $player, Item $clickedItem)
    {
        $itemName = TextFormat::clean($clickedItem->getCustomName());
        $fwPlayer = FlagWars::getPlayer($player);
        if($fwPlayer === null) return;

        if(method_exists(new ShopCategorys(), $itemName)) {
            ShopCategorys::$itemName($player);
            return;
        }

        if(empty($clickedItem->getLore()[0])) return;

        $infos = explode(" ", $clickedItem->getLore()[0]);
        if(empty($infos[0]) || empty($infos[1])) return;

        $resource = TextFormat::clean($infos[1]);
        $price = TextFormat::clean($infos[0]);

        if($resource == "Iron") {
            $resource_obj = Item::IRON_INGOT;
        }else if($resource == "Gold") {
            $resource_obj = Item::GOLD_INGOT;
        }else {
            $resource_obj = Item::BRICK;
        }

        $price = ShopManager::setPrice($player, $price, $resource_obj);
        if($price) {
            $item = $clickedItem;
            $item->setLore([]);
            if($item instanceof LeatherBoots || $item instanceof LeatherCap || $item instanceof LeatherTunic || $item instanceof LeatherPants) {
                $teamColor = $fwPlayer->getTeam()->getColor();
                $color = ShopManager::teamColorIntoColor($teamColor);
                $item->setCustomColor($color);
            }
            if($item->getId() === Item::WOOL)
                $item = Item::get(Item::WOOL, $item->getDamage(), $item->getCount());

            $player->getInventory()->addItem($item);
            $player->playSound("note.bass", 1, 2, [$player]);
        }else {
            $player->playSound("note.bass", 1, 1, [$player]);
        }

        if($itemName == "Wool" && !$price) {
            $count = ShopManager::count($player) * 4;
            $teamColor = ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor());
            $sandstone = Item::get(Item::WOOL, $teamColor, $count);
            $player->getInventory()->addItem($sandstone);
            ShopManager::rm($player, $resource_obj, ShopManager::count($player, $resource_obj));
        }
    }
}