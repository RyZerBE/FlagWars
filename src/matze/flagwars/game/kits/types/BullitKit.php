<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\FlagWars;
use matze\flagwars\game\kits\Kit;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;

class BullitKit extends Kit {

    public function __construct()
    {
        $this->setDescription("Bist Du im Besitz einer Flagge? Dieses Kit hilft dir & fügt dir 5 Extraherzen hinzu.");
        parent::__construct();
    }

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
        return "Bulli";
    }

    /** @var array  */
    private $hasFlag = [];

    /**
     * @param int $currentTick
     */
    public function onUpdate(int $currentTick): void {
        foreach ($this->getPlayers() as $player) {
            $fwPlayer = FlagWars::getPlayer($player);
            $name = $player->getName();
            if(!isset($this->hasFlag[$name])) $this->hasFlag[$name] = false;
            if($fwPlayer->hasFlag()) {
                if($this->hasFlag[$name]) {
                    continue;
                }
                $player->setMaxHealth(30);
                $player->setHealth($player->getHealth() + 10);
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 99999, 2, false));
                $this->hasFlag[$name] = true;
                continue;
            }
            if(!$this->hasFlag[$name]) {
                continue;
            }
            $this->hasFlag[$name] = false;
            $player->setMaxHealth(20);
        }
    }

    /**
     * @return bool
     */
    public function manipulatesFlagMovement(): bool {
        return true;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return 500;
    }
}