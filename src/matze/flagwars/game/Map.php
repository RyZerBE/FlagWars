<?php

namespace matze\flagwars\game;

use ryzerbe\core\util\LocationUtils;
use matze\flagwars\utils\Settings;
use pocketmine\level\Location;
use pocketmine\utils\Config;

class Map {

    /** @var string */
    private string $map;
    /** @var string */
    private mixed $creator;

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
    private ?Config $settings = null;

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
    private array $cache = [];

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
    public function getSpawner(): array {
        return $this->getSettings()->get("Spawner");
    }

    /**
     * @return Location[]
     */
    public function getShopLocations(): array {
        $spawner = [];
        foreach ($this->getSettings()->get("Shops") as $data) {
            $spawner[] = LocationUtils::fromString($data);
        }
        return $spawner;
    }

    /**
     * @return Location
     */
    public function getRandomFlagLocation(): Location {
        if(!isset($this->cache["FlagLocations"])) {
            $locations = [];
            foreach ($this->getSettings()->get("FlagLocations") as $location) {
                $locations[] = $location;
            }
            $this->cache["FlagLocations"] = $locations;
        }
        $location = $this->cache["FlagLocations"][array_rand($this->cache["FlagLocations"])];
        return LocationUtils::fromString($location);
    }

    /**
     * @return Location
     */
    public function getSpectatorLocation(): Location {
        if(!isset($this->cache["SpectatorLocation"])) $this->cache["Flag"] = LocationUtils::fromString($this->getSettings()->get("SpectatorLocation"));
        return $this->cache["SpectatorLocation"];
    }
}