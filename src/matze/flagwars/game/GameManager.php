<?php

namespace matze\flagwars\game;

use matze\flagwars\game\kits\Kit;
use matze\flagwars\utils\InstantiableTrait;
use matze\flagwars\utils\Settings;
use onebone\economyapi\event\Issuer;
use pocketmine\Player;
use pocketmine\Server;

class GameManager {
    use InstantiableTrait;

    public function __construct() {
        $kits = [

        ];
        foreach ($kits as $kit) {
            $this->registerKit($kit);
        }
    }

    /** @var array  */
    private $kits = [];

    /**
     * @return array
     */
    public function getKits(): array {
        return $this->kits;
    }

    /**
     * @param string $kit
     * @return Kit|null
     */
    public function getKit(string $kit): ?Kit {
        return isset($this->kits[$kit]) ? $this->kits[$kit] : null;
    }

    /**
     * @param Kit $kit
     */
    public function registerKit(Kit $kit): void {
        $this->kits[$kit->getName()] = $kit;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int {
        return Settings::$players_per_team * Settings::$total_teams;
    }

    /** @var array  */
    private $players = [];

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player): void {
        $this->players[] = $player->getName();
    }

    /**
     * @param Player|string $player
     */
    public function removePlayer($player): void {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        if(!$this->isPlayer($player)) {
            return;
        }
        unset($this->players[array_search($player, $this->players)]);
    }

    /**
     * @param Player|string $player
     * @return bool
     */
    public function isPlayer($player): bool {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        return in_array($player, $this->players);
    }

    /**
     * @return array
     */
    public function getPlayers(): array {
        $players = [];
        foreach ($this->players as $player) {
            $player = Server::getInstance()->getPlayerExact($player);
            if(!is_null($player)) $players[] = $player;
        }
        return $players;
    }

    /** @var bool  */
    private $stats = true;

    /**
     * @param bool $enabled
     */
    public function setStatsEnabled(bool $enabled): void {
        $this->stats = $enabled;
    }

    /**
     * @return bool
     */
    public function statsEnabled(): bool {
        return $this->stats;
    }

    /** @var bool  */
    private $forceStart = false;

    /**
     * @param bool $forceStart
     */
    public function setForceStart(bool $forceStart): void {
        $this->forceStart = $forceStart;
    }

    /**
     * @return bool
     */
    public function isForceStart(): bool {
        return $this->forceStart;
    }

    public const STATE_WAITING = 1;
    public const STATE_COUNTDOWN = 2;
    public const STATE_INGAME = 3;
    public const STATE_RESTART = 4;

    /** @var int  */
    private $state = self::STATE_WAITING;

    /**
     * @return int
     */
    public function getState(): int {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state): void {
        if($this->state === $state) {
            return;
        }
        $this->state = $state;

        switch ($state) {
            case self::STATE_COUNTDOWN: {
                $this->setCountdown(Settings::$waiting_countdown);
                break;
            }
        }
    }

    /**
     * @return bool
     */
    public function isIngame(): bool{
        return $this->getState() === self::STATE_INGAME;
    }

    /**
     * @return bool
     */
    public function isRestarting(): bool {
        return $this->getState() === self::STATE_RESTART;
    }

    /**
     * @return bool
     */
    public function isWaiting(): bool {
        return $this->getState() === self::STATE_WAITING;
    }

    /**
     * @return bool
     */
    public function isCountdown(): bool {
        return $this->getState() === self::STATE_COUNTDOWN;
    }

    /** @var int  */
    private $countdown = 60;

    /**
     * @param int $countdown
     */
    public function setCountdown(int $countdown): void {
        $this->countdown = $countdown;
    }

    /**
     * @return int
     */
    public function getCountdown(): int {
        return $this->countdown;
    }

    public function tickCountdown(): void {
        if($this->getCountdown() <= 0) {
            return;
        }
        $this->countdown--;
    }
}