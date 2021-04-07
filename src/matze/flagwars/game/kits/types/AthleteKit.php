<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\FlagWars;
use matze\flagwars\game\kits\Kit;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\Player;

class AthleteKit extends Kit {

    public function __construct()
    {
        $this->setDescription("Mit diesem Kit bist Du ein echter Athlet! Du kannst mit der Flagge schneller rennen als alle anderen!");
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
        return "Athlet";
    }

    /**
     * @param PlayerToggleSprintEvent $event
     */
    public function onToggleSprint(PlayerToggleSprintEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->isPlayer($player)) return;
        $fwPlayer = FlagWars::getPlayer($player);
        if(!$fwPlayer->hasFlag()) return;
        if($event->isSprinting()) {
            $player->removeEffect(Effect::SLOWNESS);
            return;
        }
        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 99999, 1, false));
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