<?php

namespace matze\flagwars\game;

use pocketmine\Player;

class Team {

    /** @var string */
    private $name;

    /** @var string */
    private $color;

    /** @var array  */
    private $players = [];

    /**
     * Team constructor.
     * @param string $name
     * @param string $color
     */
    public function __construct(string $name, string $color) {
        $this->name = $name;
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getColor(): string {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPlayers(): array {
        return $this->players;
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player): void {
        $this->players[$player->getName()] = $player;
    }

    /**
     * @param $player
     */
    public function removePlayer($player): void {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        if(!isset($this->players[$player])) {
            return;
        }
        unset($this->players[$player]);
    }
}