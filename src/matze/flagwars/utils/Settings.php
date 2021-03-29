<?php

namespace matze\flagwars\utils;

use pocketmine\utils\Config;

class Settings {

    /**
     * Settings constructor.
     */
    public function __construct() {
        $settings = new Config("plugins/settings.yml");

        self::$players_per_team = $settings->get("PlayersPerTeam", 1);
        self::$total_teams = $settings->get("TotalTeams", 2);
        self::$waiting_countdown = $settings->get("WaitingCountdown", 10);
        self::$waiting_lobby_location = LocationUtils::fromString($settings->get("WaitingLobbyLocation", "0:5:0:0:0"));
    }

    public static $players_per_team;
    public static $total_teams;
    public static $waiting_countdown;
    public static $waiting_lobby_location;
}