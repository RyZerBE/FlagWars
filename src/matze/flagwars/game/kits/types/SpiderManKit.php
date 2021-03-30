<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\game\GameManager;
use matze\flagwars\game\kits\Kit;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

class SpiderManKit extends Kit {

    /**
     * @param Player $player
     * @return array
     */
    public function getItems(Player $player): array {
        return [];
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Spiderman";
    }

    /** @var array  */
    private $blocks = [];

    /**
     * @param int $currentTick
     */
    public function onUpdate(int $currentTick): void {
        $game = GameManager::getInstance();
        $level = Server::getInstance()->getLevelByName($game->getMap()->getName());
        if(isset($this->blocks[$currentTick])) {
            foreach ($this->blocks[$currentTick] as $block) {
                $level->setBlockIdAt($block->x, $block->y, $block->z, 0);
            }
            unset($this->blocks[$currentTick]);
        }


        foreach ($this->getPlayers() as $player) {
            //Don`t mind this trash code
            for ($y = 0; $y <= 1; $y++) {
                $block = $level->getBlock($player->add(0, $y, 0));
                $frontBlock = $this->getFrontBlock($player, $player->y + $y);

                if($frontBlock->isSolid() && $block->canBeReplaced() && !$block->canClimb()) {
                    $faces = [0 => 8, 1 => 1, 2 => 2, 3 => 4, 4 => 8];
                    $meta = $faces[$player->getDirection()] ?? 0;
                    $level->setBlockIdAt($block->x, $block->y, $block->z, Block::VINE);
                    $level->setBlockDataAt($block->x, $block->y, $block->z, $meta);

                    if(!isset($this->blocks[($currentTick + 20)])) $this->blocks[($currentTick + 20)] = [];
                    $this->blocks[($currentTick + 20)][] = $block;
                }
            }
        }
    }

    /**
     * @param Player $player
     * @param int|null $y
     * @return Block
     */
    private function getFrontBlock(Player $player, ?int $y = null) : Block {
        $y = $y ?? $player->y;
        switch ($player->getDirection()){
            case 0: return $player->getLevel()->getBlock(new Vector3($player->x + 0.5, $y, $player->z));//South
            case 1: return $player->getLevel()->getBlock(new Vector3($player->x, $y, $player->z + 0.5));//West
            case 2: return $player->getLevel()->getBlock(new Vector3($player->x - 0.5, $y, $player->z));//North
            case 3: return $player->getLevel()->getBlock(new Vector3($player->x, $y, $player->z - 0.5));//East
            default: return $player->getLevel()->getBlock(new Vector3($player->x, $y, $player->z));
        }
    }
}