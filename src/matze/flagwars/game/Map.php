<?php

namespace matze\flagwars\game;

use matze\flagwars\utils\LocationUtils;
use matze\flagwars\utils\Settings;
use pocketmine\level\Location;
use pocketmine\utils\Config;

class Map {

    /** @var string */
    private $map;
    /** @var string */
    private $creator;

    /**
     * Map constructor.
     * @param string $map
     */
    public function __construct(string $map) {
        $this->map = $map;
        $this->creator = $this->getSettings()->get("Creator", "N/A");
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->map;
    }

    /**
     * @return string
     */
    public function getCreator(): string {
        return $this->creator;
    }

    /** @var Config|null */
    private $settings = null;

    /**
     * @return Config
     */
    public function getSettings(): Config {
        if(is_null($this->settings)) {
            $this->settings = new Config(Settings::MAPS_PATH . $this->getName() . "/settings.yml");
        }
        return $this->settings;
    }

    /** @var array  */
    private $cache = [];

    /**
     * @param Team $team
     * @return Location
     */
    public function getTeamSpawnLocation(Team $team): Location {
        if(!isset($this->cache[$team->getName()])) $this->cache[$team->getName()] = [];
        if(!isset($this->cache[$team->getName()]["SpawnLocation"])) {
            $this->cache[$team->getName()]["SpawnLocation"] = LocationUtils::fromString($this->getSettings()->getNested("Spawns." . $team->getName()));
        }
        return $this->cache[$team->getName()]["SpawnLocation"];
    }

    /**
     * @return array
     */
    public function getSpawnerLocations(): array {
        $spawner = [];
        foreach ($this->getSettings()->get("Spawner") as $location => $data) {
            $spawner[] = LocationUtils::fromString($location);
        }
        return $spawner;
    }

    /**
     * @return array
     */
    public function getShopLocations(): array {
        $spawner = [];
        foreach ($this->getSettings()->get("Shops") as $type => $data) {
            $spawner[] = LocationUtils::fromString($data);
        }
        return $spawner;
    }

    /**
     * @return Location
     */
    public function getFlagLocation(): Location {
        if(!isset($this->cache["Flag"])) $this->cache["Flag"] = [];
        if(!isset($this->cache["Flag"]["SpawnLocation"])) {
            $this->cache["Flag"]["SpawnLocation"] = LocationUtils::fromString($this->getSettings()->get("FlagLocation"));
        }
        return $this->cache["Flag"]["SpawnLocation"];
    }
}