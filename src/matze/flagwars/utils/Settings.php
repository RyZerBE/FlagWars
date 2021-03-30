<?php

namespace matze\flagwars\utils;

use pocketmine\block\Block;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class Settings {

    public const MAPS_PATH = "maps/";
    public const SKIN_PATH = "skins/";

    /**
     * Settings constructor.
     */
    public function __construct() {
        $settings = new Config("plugins/settings.yml", Config::YAML, [
            "PlayersPerTeam" => 1,
            "TotalTeams" => 2,
            "WaitingCountdown" => 10,//todo: 60
            "WaitingLobbyLocation" => "0:5:0:0:0",
            "MapPool" => [],
            "PlayersForStart" => 2,
            "BronzeSpawnDelay" => [1 => 40, 2 => 20, 3 => 10],
            "IronSpawnDelay" => [1 => 600, 2 => 300, 3 => 200],
            "GoldSpawnDelay" => [1 => 1200, 2 => 600, 3 => 400],
            "BronzeSpawnerUpgradeCost" => [2 => 64, 3 => 128],
            "IronSpawnerUpgradeCost" => [2 => 10, 3 => 20],
            "GoldSpawnerUpgradeCost" => [2 => 4, 3 => 8],
            "FlagsToWin" => 3
        ]);

        self::$players_per_team = $settings->get("PlayersPerTeam");
        self::$total_teams = $settings->get("TotalTeams");
        self::$waiting_countdown = $settings->get("WaitingCountdown");
        self::$waiting_lobby_location = LocationUtils::fromString($settings->get("WaitingLobbyLocation"));
        self::$map_pool = $settings->get("MapPool");
        self::$players_for_start = $settings->get("PlayersForStart");
        self::$bronze_spawn_delay = $settings->get("BronzeSpawnDelay");
        self::$iron_spawn_delay = $settings->get("IronSpawnDelay");
        self::$gold_spawn_delay = $settings->get("GoldSpawnDelay");
        self::$bronze_upgrade_cost = $settings->get("BronzeSpawnerUpgradeCost");
        self::$iron_upgrade_cost = $settings->get("IronSpawnerUpgradeCost");
        self::$gold_upgrade_cost = $settings->get("GoldSpawnerUpgradeCost");
        self::$flag_to_win = $settings->get("FlagsToWin");
    }

    public static $players_per_team;
    public static $total_teams;
    public static $waiting_countdown;
    public static $waiting_lobby_location;
    public static $map_pool;
    public static $players_for_start;
    public static $bronze_spawn_delay;
    public static $iron_spawn_delay;
    public static $gold_spawn_delay;
    public static $bronze_upgrade_cost;
    public static $iron_upgrade_cost;
    public static $gold_upgrade_cost;
    public static $flag_to_win;

    /**
     * @var array
     */
    public static $teams = [
        1 => ["Name" => "Red", "Color" => C::RED],
        2 => ["Name" => "Blue", "Color" => C::BLUE],
        //todo: all colors
    ];

    /**
     * @var array
     */
    public static $breakableBlocks = [
        Block::RED_MUSHROOM,
        Block::BROWN_MUSHROOM,
        31
    ];

    /**
     * @var array
     */
    public static $notInteractAbleBlocks = [
        Block::CHEST, Block::FURNACE, Block::WORKBENCH
    ];
}