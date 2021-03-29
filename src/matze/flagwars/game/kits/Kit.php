<?php

namespace matze\flagwars\game\kits;

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

    abstract public function getItems(): array;
    abstract public function onUpdate(Player $player): void;
    abstract public function getName(): string;
}