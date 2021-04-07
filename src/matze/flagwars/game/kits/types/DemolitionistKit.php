<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\game\kits\Kit;
use matze\flagwars\shop\ShopManager;
use matze\flagwars\utils\ItemUtils;
use matze\flagwars\utils\Vector3Utils;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use BauboLP\Core\Utils\TNT;

class DemolitionistKit extends Kit {

    public function __construct()
    {
        $this->setDescription("Mit diesem Kit kannst Du TNT aus der Ferne zünden. BOOOMM!");
        parent::__construct();
    }

    /**
     * @param Player $player
     * @return array
     */
    public function getItems(Player $player): array {
        return [
            ItemUtils::addItemTags(Item::get(Item::TNT)->setCustomName("§r§fRemote TNT"), ["remote_tnt" => ""])
        ];
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Sprengmeister";
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority HIGH
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->isPlayer($player)) return;
        $block = $event->getBlock();
        $item = $event->getItem();

        if(!ItemUtils::hasItemTag($item, "remote_tnt")) {
            return;
        }
        if(ItemUtils::hasItemTag($item, "position")) {
            $event->setCancelled();
            return;
        }
        $position = Vector3Utils::toString($block->floor());
        $newItem = ItemUtils::addItemTags(Item::get(Item::REDSTONE_TORCH)->setCustomName("§r§fFuse Remote TNT"), [
            "position" => $position,
            "remote_tnt" => "",
            "kit_item" => "kit_item"
        ]);
        $player->getInventory()->setItemInHand($newItem);
        $player->resetItemCooldown($newItem, 5);

        GameManager::getInstance()->removeBlock($block);
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->isPlayer($player)) return;
        $fwPlayer = FlagWars::getPlayer($player);
        $item = $event->getItem();
        if(!ItemUtils::hasItemTag($item, "remote_tnt") || !ItemUtils::hasItemTag($item, "position") || $player->hasItemCooldown($item)) {
            return;
        }
        $position = Vector3Utils::fromString(ItemUtils::getItemTag($item, "position"));
        $block = $player->getLevel()->getBlock($position);

        if(!$block instanceof TNT) {
            return;
        }
        $block->ignite(1, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor()));
        $player->getInventory()->setItemInHand(Item::get(0));
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return 500;
    }
}