<?php


namespace matze\flagwars\listener;


use matze\flagwars\FlagWars;
use matze\flagwars\shop\ShopManager;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\Player;

class InventoryPickUpListener implements Listener
{

    public function pickup(InventoryPickupItemEvent $event)
    {
        $itemEntity = $event->getItem();
        $item = $itemEntity->getItem();
        if ($item->getId() === Item::WOOL) {
            $event->setCancelled();
            foreach ($itemEntity->getViewers() as $player) {
                if (!$player instanceof Player) continue;

                $fwPlayer = FlagWars::getPlayer($player);
                if ($fwPlayer === null) continue;

                if ($player->distance($itemEntity->asVector3()) < 1.2) {
                    $itemEntity->close();
                    $player->getInventory()->addItem(Item::get(Item::WOOL, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor()), $item->getCount()));
                    $player->playSound("random.pop", 1.0, 1.0, [$player]);
                    break;
                }
            }
        }
    }
}