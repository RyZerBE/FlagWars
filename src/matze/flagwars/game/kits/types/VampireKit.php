<?php

namespace matze\flagwars\game\kits\types;

use matze\flagwars\FlagWars;
use matze\flagwars\game\kits\Kit;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class VampireKit extends Kit {

    public function __construct()
    {
        $this->setDescription("Dein Team bekommt für jeden Kill 2.5 Herzen dazu, dafür läufst du aber mit der Flagge noch langsamer als normal!");
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
        return "Vampire";
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);

        $killer = $fwPlayer->getLastDamager();
        if(is_null($killer)) return;
        if(!$this->isPlayer($killer)) return;
        $killer->setHealth($killer->getHealth() + 5);
        $fwKiller = FlagWars::getPlayer($killer);

        foreach ($fwKiller->getTeam()->getPlayers() as $player) {
            $player->setHealth($player->getHealth() + 5);
            $player->sendActionBarMessage(TextFormat::GREEN."+2 ".TextFormat::RED."❤");
        }
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return 25000;
    }
}