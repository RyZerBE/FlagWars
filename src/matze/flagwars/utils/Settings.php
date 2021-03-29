<?php

namespace matze\flagwars\utils;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class Settings {

    /** @var string  */
    public const MAPS_PATH = "maps/";

    /**
     * Settings constructor.
     */
    public function __construct() {
        $settings = new Config("plugins/settings.yml");

        self::$players_per_team = $settings->get("PlayersPerTeam", 1);
        self::$total_teams = $settings->get("TotalTeams", 2);
        self::$waiting_countdown = $settings->get("WaitingCountdown", 10);
        self::$waiting_lobby_location = LocationUtils::fromString($settings->get("WaitingLobbyLocation", "0:5:0:0:0"));
        self::$map_pool = $settings->get("MapPool", []);
        self::$players_for_start = $settings->get("PlayersForStart", 2);
    }

    public static $players_per_team;
    public static $total_teams;
    public static $waiting_countdown;
    public static $waiting_lobby_location;
    public static $map_pool;
    public static $players_for_start;

    /**
     * @var array
     */
    public static $teams = [
        1 => ["Name" => "Red", "Color" => C::RED],
        2 => ["Name" => "Blue", "Color" => C::BLUE],
        //todo: all colors
    ];
}