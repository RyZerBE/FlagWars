<?php

namespace matze\flagwars\game\kits;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\Loader;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

abstract class Kit implements Listener {

    /**
     * Kit constructor.
     */
    public function __construct() {
        Server::getInstance()->getPluginManager()->registerEvents($this, Loader::getInstance());
    }

    abstract public function getItems(Player $player): array;
    abstract public function getName(): string;

    /**
     * @param int $currentTick
     */
    public function onUpdate(int $currentTick): void {}

    /**
     * @return bool
     */
    public function manipulatesFlagMovement(): bool {
        return false;
    }

    /**
     * @return bool
     */
    public function getItemsOnRespawn(): bool {
        return true;
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array {
        $players = [];
        foreach (GameManager::getInstance()->getPlayers() as $player) {
            $fwPlayer = FlagWars::getPlayer($player);
            $kit = $fwPlayer->getKit();
            if(is_null($kit)) continue;
            if($kit->getName() !== $this->getName()) continue;
            $players[] = $player;
        }
        return $players;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isPlayer(Player $player): bool {
        $kit = FlagWars::getPlayer($player)->getKit();
        return !is_null($kit) && $kit->getName() === $this->getName();
    }
}