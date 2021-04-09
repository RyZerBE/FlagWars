<?php


namespace matze\flagwars\listener;


use matze\flagwars\entity\ShopEntity;
use matze\flagwars\FlagWars;
use matze\flagwars\shop\categories\RushCategory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEntityEvent;

class PlayerInteractEntityListener implements Listener
{

    public function interactEntity(PlayerInteractEntityEvent $event)
    {
        $entity = $event->getEntity();
        $player = $event->getPlayer();

        if($entity instanceof ShopEntity) {
            $fwPlayer = FlagWars::getPlayer($player);
            if($fwPlayer === null) return;

            $fwPlayer->getShopMenu()->updateCategory(new RushCategory());
            $fwPlayer->getShopMenu()->open();
        }
    }
}