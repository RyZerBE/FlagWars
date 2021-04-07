<?php

namespace matze\flagwars\provider;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\shop\ShopManager;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use pocketmine\Server;

class FlagWarsProvider
{

    /**
     * @param \pocketmine\Player $player
     */
    public static function createWall(Player $player): void
    {
        $fwPlayer = FlagWars::getPlayer($player);
        if ($fwPlayer === null) return;

        $pos = [];
        $player->getInventory()->removeItem(Item::get(Item::LEVER, 0, 1));
        $direction = $player->getDirection();
        $arena = GameManager::getInstance();

        switch ($direction) {
            case Entity::SIDE_WEST:
            case Entity::SIDE_EAST:
                //TODO: USELESS
                break;
            case Entity::SIDE_NORTH:
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
            case Entity::SIDE_SOUTH:
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
            case Entity::SIDE_UP:
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
            case Entity::SIDE_DOWN:
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
            if ($block->getId() === Block::AIR) {
                $level->setBlock($position, Block::get(Block::WOOL, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor())));
                $arena->addBlock($block);
            }
        }
    }

    public static function createSafetyPlatform(Player $player): void
    {
        $fwPlayer = FlagWars::getPlayer($player);
        if ($fwPlayer === null) return;

        $player->getInventory()->removeItem(Item::get(Item::BLAZE_ROD, 0, 1));
        $arena = GameManager::getInstance();
        $playerVec = $player->asVector3()->add(0, 0.5);
        for ($x = -1; $x <= 1; $x++) {
            for ($z = -1; $z <= 1; $z++) {
                $vec = $player->add($x, -1, $z);
                $block = $player->getLevel()->getBlockAt($vec->x, $vec->y, $vec->z);
                if ($block->getId() === Block::AIR) {
                    $player->getLevel()->setBlock($vec, Block::get(Block::WOOL, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor())));
                    $arena->addBlock($block);
                }
            }
        }
        $player->teleport($playerVec);
    }

    /**
     * @param Player[] $players
     * @param \pocketmine\math\Vector3 $vector3
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
        /** @var Player $player */
        foreach ($players as $player) {
            $player->sendDataPacket($light);
            $player->playSound("ambient.weather.lightning.impact", 2.0, 1.0, [$player]);
        }
    }
}