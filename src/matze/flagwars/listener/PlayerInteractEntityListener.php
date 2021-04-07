<?php


namespace matze\flagwars\listener;


use matze\flagwars\entity\ShopEntity;
use matze\flagwars\shop\ShopCategorys;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEntityEvent;

class PlayerInteractEntityListener implements Listener
{

    public function interactEntity(PlayerInteractEntityEvent $event)
    {
        $entity = $event->getEntity();
        $player = $event->getPlayer();

        if($entity instanceof ShopEntity)
            ShopCategorys::RushCategory($player);
    }
}