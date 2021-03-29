<?php

namespace matze\flagwars\game;

use matze\flagwars\FlagWars;
use matze\flagwars\game\kits\Kit;
use matze\flagwars\utils\AsyncExecuter;
use matze\flagwars\utils\FileUtils;
use matze\flagwars\utils\InstantiableTrait;
use matze\flagwars\utils\Settings;
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

        foreach (Settings::$map_pool as $map) {
            $this->registerMap(new Map($map));
        }

        for ($int = 1; $int <= Settings::$total_teams; $int++) {
            $team = Settings::$teams[$int];
            $this->registerTeam(new Team($team["Name"], $team["Color"]));
        }
    }

    /** @var array  */
    private $teams = [];

    /**
     * @return array
     */
    public function getTeams(): array {
        return $this->teams;
    }

    /**
     * @param string $team
     * @return Team|null
     */
    public function getTeam(string $team): ?Team {
        return $this->teams[$team] ?? null;
    }

    /**
     * @param Team $team
     */
    public function registerTeam(Team $team): void {
        $this->teams[$team->getName()] = $team;
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
        return $this->kits[$kit] ?? null;
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

    /** @var array  */
    private $map_pool = [];

    /**
     * @return array
     */
    public function getMapPool(): array {
        return $this->map_pool;
    }

    /**
     * @param Map $map
     */
    public function registerMap(Map $map): void {
        $this->map_pool[$map->getName()] = $map;
    }

    /**
     * @param string $map
     * @return Map|null
     */
    public function getMapByName(string $map): ?Map {
        return $this->map_pool[$map] ?? null;
    }

    public function loadMap(): void {
        //I hate those things....
        $maps = [];
        foreach ($this->getMapPool() as $mapName => $map) {
            $maps[] = $mapName;
        }
        $mapVotes = [];
        foreach ($this->getPlayers() as $player) {
            $fwPlayer = FlagWars::getPlayer($player);
            if(!in_array($fwPlayer->getMapVote(), $maps)) continue;
            if(!isset($mapVotes[$fwPlayer->getMapVote()])) $mapVotes[$fwPlayer->getMapVote()] = 0;
            $mapVotes[$fwPlayer->getMapVote()]++;
        }
        krsort($mapVotes);
        $maps = [];
        $index = 1;
        foreach ($mapVotes as $map => $votes) {
            if(!isset($maps[$index])) $maps[$index] = [];
            $maps[$index][] = $map;
        }
        $topMaps = $maps[1];
        $map = $topMaps[array_rand($topMaps)];
        AsyncExecuter::submitAsyncTask(function () use ($map): void {
            if(is_dir("worlds/" . $map)) {
                FileUtils::delete("worlds/" . $map);
            }
            @mkdir("worlds/" . $map);
            FileUtils::copy(Settings::MAPS_PATH . $map, "worlds/" . $map);
        }, function (Server $server, $result) use ($map): void {
            $server->loadLevel($map);
            $map = $server->getLevelByName($map);
            $this->setMap($this->getMapByName($map->getFolderName()));
        });
    }

    /** @var Map|null */
    private $map = null;

    /**
     * @return Map|null
     */
    public function getMap(): ?Map {
        return $this->map;
    }

    /**
     * @param Map|null $map
     */
    public function setMap(?Map $map): void {
        $this->map = $map;
    }
}