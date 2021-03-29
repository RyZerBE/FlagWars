<?php

namespace matze\flagwars\forms;

use pocketmine\Player;

abstract class Form {
    abstract public function open(Player $player, int $window = -1, array $extraData = []): void;
}