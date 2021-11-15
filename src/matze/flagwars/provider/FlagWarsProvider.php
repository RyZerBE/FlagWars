<?php

namespace matze\flagwars\provider;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\shop\ShopManager;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use pocketmine\Server;

class FlagWarsProvider
{

    /**
     * @param Player $player
     */
    public static function createWall(Player $player): void
    {
        $fwPlayer = FlagWars::getPlayer($player);
        if ($fwPlayer === null) return;

        $pos = [];
        $player->getInventory()->removeItem(Item::get(BlockIds::LEVER));
        $direction = $player->getDirection();
        $arena = GameManager::getInstance();

        switch ($direction) {
            case Vector3::SIDE_WEST:
            case Vector3::SIDE_EAST:
                //TODO: USELESS
                break;
            case Vector3::SIDE_NORTH:
                $pos = [];
                $pos[] = $player->asVector3()->add(-2);
                $pos[] = $player->asVector3()->add(-2, 0, -1);
                $pos[] = $player->asVector3()->add(-2, 0, 1);

                $pos[] = $player->asVector3()->add(-2, 1);
                $pos[] = $player->asVector3()->add(-2, 1, -1);
                $pos[] = $player->asVector3()->add(-2, 1, 1);

                $pos[] = $player->asVector3()->add(-2, 2);
                $pos[] = $player->asVector3()->add(-2, 2, -1);
                $pos[] = $player->asVector3()->add(-2, 2, 1);
                break;
            case Vector3::SIDE_SOUTH:
                $pos[] = $player->asVector3()->add(0, 0, -2);
                $pos[] = $player->asVector3()->add(1, 0, -2);
                $pos[] = $player->asVector3()->add(-1, 0, -2);

                $pos[] = $player->asVector3()->add(1, 1, -2);
                $pos[] = $player->asVector3()->add(-1, 1, -2);
                $pos[] = $player->asVector3()->add(0, 1, -2);

                $pos[] = $player->asVector3()->add(1, 2, -2);
                $pos[] = $player->asVector3()->add(-1, 2, -2);
                $pos[] = $player->asVector3()->add(0, 2, -2);
                break;
            case Vector3::SIDE_UP:
                $pos[] = $player->asVector3()->add(0, 0, +2);
                $pos[] = $player->asVector3()->add(1, 0, +2);
                $pos[] = $player->asVector3()->add(-1, 0, +2);

                $pos[] = $player->asVector3()->add(1, 1, +2);
                $pos[] = $player->asVector3()->add(-1, 1, +2);
                $pos[] = $player->asVector3()->add(0, 1, +2);

                $pos[] = $player->asVector3()->add(1, 2, +2);
                $pos[] = $player->asVector3()->add(-1, 2, +2);
                $pos[] = $player->asVector3()->add(0, 2, +2);
                break;
            case Vector3::SIDE_DOWN:
                $pos[] = $player->asVector3()->add(+2);
                $pos[] = $player->asVector3()->add(+2, 0, -1);
                $pos[] = $player->asVector3()->add(+2, 0, 1);

                $pos[] = $player->asVector3()->add(+2, 1);
                $pos[] = $player->asVector3()->add(+2, 1, -1);
                $pos[] = $player->asVector3()->add(+2, 1, 1);

                $pos[] = $player->asVector3()->add(+2, 2);
                $pos[] = $player->asVector3()->add(+2, 2, -1);
                $pos[] = $player->asVector3()->add(+2, 2, 1);
                break;
        }


        $level = $player->getLevel();
        /** @var Vector3 $position */
        foreach ($pos as $position) {
            $block = $level->getBlock($position);
            if ($block->getId() === BlockIds::AIR) {
                $level->setBlock($position, Block::get(BlockIds::WOOL, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor())));
                $arena->addBlock($block);
            }
        }
    }

    public static function createSafetyPlatform(Player $player): void
    {
        $fwPlayer = FlagWars::getPlayer($player);
        if ($fwPlayer === null) return;

        $player->getInventory()->removeItem(Item::get(ItemIds::BLAZE_ROD));
        $arena = GameManager::getInstance();
        $playerVec = $player->asVector3()->add(0, 0.5);
        for ($x = -1; $x <= 1; $x++) {
            for ($z = -1; $z <= 1; $z++) {
                $vec = $player->add($x, -1, $z);
                $block = $player->getLevel()->getBlockAt($vec->x, $vec->y, $vec->z);
                if ($block->getId() === BlockIds::AIR) {
                    $player->getLevel()->setBlock($vec, Block::get(BlockIds::WOOL, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor())));
                    $arena->addBlock($block);
                }
            }
        }
        $player->teleport($playerVec);
    }

    /**
     * @param Player[] $players
     * @param Vector3 $vector3
     */
    public static function createStrike(Vector3 $vector3, array $players = [])
    {
        if(count($players) <= 0 || in_array("ALL", $players))
            $players = Server::getInstance()->getOnlinePlayers();

        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->metadata = [];
        $light->position = new Vector3($vector3->x, $vector3->y, $vector3->z);
        $light->entityRuntimeId = Entity::$entityCount++;
        foreach ($players as $player) {
            $player->sendDataPacket($light);
            $player->playSound("ambient.weather.lightning.impact", 2.0, 1.0, [$player]);
        }
    }
}