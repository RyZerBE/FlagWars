<?php

namespace matze\flagwars\game;

use matze\flagwars\utils\Settings;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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
     * @return Player[]
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

    /**
     * @param $player
     * @return bool
     */
    public function isPlayer($player): bool {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        return isset($this->players[$player]);
    }

    /**
     * @return bool
     */
    public function isFull(): bool {
        return count($this->getPlayers()) >= Settings::$players_per_team;
    }

    /**
     * @return Location
     */
    public function getSpawnLocation(): Location {
        if(is_null(GameManager::getInstance()->getMap())) return new Location();
        $location = clone GameManager::getInstance()->getMap()->getTeamSpawnLocation($this);
        $level = $location->getLevel();
        while (
            $level->getBlock($location)->isSolid() ||
            $level->getBlock($location->add(0, 1, 0))->isSolid()
        ) {
            $location->y++;
        }
        return $location;
    }

    /**
     * @return bool
     */
    public function isAlive(): bool {
        return count($this->getPlayers()) > 0;
    }

    /** @var bool  */
    private $hasFlag = false;

    /**
     * @param bool $hasFlag
     */
    public function setHasFlag(bool $hasFlag): void {
        $this->hasFlag = $hasFlag;
    }

    /**
     * @return bool
     */
    public function hasFlag(): bool {
        return $this->hasFlag;
    }

    /** @var int  */
    private $flagsSaved = 0;

    /**
     * @return int
     */
    public function getFlagsSaved(): int {
        return $this->flagsSaved;
    }

    public function addFlagsSaved(): void {
        $this->flagsSaved++;
    }

    /**
     * @return int
     */
    public function getBlockMeta(): int {
        switch ($this->getColor()) {
            case TextFormat::RED:
                return 14;
            case TextFormat::BLUE:
            case TextFormat::AQUA:
                return 11;
            case TextFormat::YELLOW:
                return 4;
            case TextFormat::GREEN:
            case TextFormat::DARK_GREEN:
                return 5;
            case TextFormat::LIGHT_PURPLE:
                return 6;
            case TextFormat::GOLD:
                return 1;
            case TextFormat::DARK_PURPLE:
                return 10;
        }
        return 0;
    }
}
