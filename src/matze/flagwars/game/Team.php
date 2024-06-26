<?php

namespace matze\flagwars\game;

use matze\flagwars\FlagWars;
use matze\flagwars\player\FlagWarsPlayer;
use matze\flagwars\utils\Settings;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\RyZerPlayerProvider;

class Team {

    /** @var string */
    private string $name;

    /** @var string */
    private string $color;

    /** @var array  */
    private array $players = [];

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
     * @param FlagWarsPlayer $player
     */
    public function join(FlagWarsPlayer $player){
        $rbePlayer = RyZerPlayerProvider::getRyzerPlayer($player->getPlayer());
        if($rbePlayer === null) return;

        $this->addPlayer($player->getPlayer());
        $player->setTeam($this);
        $player->getPlayer()->sendMessage(FlagWars::PREFIX.LanguageProvider::getMessageContainer("team-joined", $player->getPlayer()->getName(), ["#team" => $this->getColor().$this->getName()]));
        $player->getPlayer()->setNameTag($this->getColor().$rbePlayer->getName(true));
        $player->getPlayer()->setDisplayName($this->getColor().$rbePlayer->getName(true));
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
            $level->getBlock($location->add(0, 1))->isSolid()
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
    private bool $hasFlag = false;

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
    private int $flagsSaved = 0;

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
        return match ($this->getColor()) {
            TextFormat::RED => 14,
            TextFormat::BLUE, TextFormat::AQUA => 11,
            TextFormat::YELLOW => 4,
            TextFormat::GREEN, TextFormat::DARK_GREEN => 5,
            TextFormat::LIGHT_PURPLE => 6,
            TextFormat::GOLD => 1,
            TextFormat::DARK_PURPLE => 10,
            default => 0,
        };
    }
}
