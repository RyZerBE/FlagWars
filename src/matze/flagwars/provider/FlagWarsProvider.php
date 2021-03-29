<?php

namespace matze\flagwars\provider;

use pocketmine\utils\Config;

class FlagWarsProvider {

    /** @var string */
    private static $host;
    /** @var string */
    private static  $user;
    /** @var string */
    private static $password;
    /** @var string */
    private static $database;

    /**
     * FlagWarsProvider constructor.
     */
    public function __construct() {
        $mysqlSettings = new Config("mysql.yml");
        self::$host = $mysqlSettings->get("Host");
        self::$user = $mysqlSettings->get("User");
        self::$password = $mysqlSettings->get("Password");
        self::$database = $mysqlSettings->get("Database");
    }

    /**
     * @param string $player
     */
    public static function checkRegistered(string $player): void {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT * FROM flagwars_stats WHERE player = ´" . $player . "´");
        if(($statement->num_rows > 0)) {
            return;
        }
        $mysql->query("INSERT INTO ´flagwars_stats´(´player´) VALUES (´" . $player . "´)");
        $mysql->query("INSERT INTO ´flagwars_kits´(´player´) VALUES (´" . $player . "´)");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getKills(string $player): int {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_kills FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $kills
     */
    public static function setKills(string $player, int $kills): void {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_kills = ´" . $kills . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getDeaths(string $player): int {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_deaths FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $deaths
     */
    public static function setDeaths(string $player, int $deaths): void {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_deaths = ´" . $deaths . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getGamePlays(string $player): int {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_plays FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $game_plays
     */
    public static function setGamePlays(string $player, int $game_plays): void {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_plays = ´" . $game_plays . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getGameWins(string $player): int {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_wins FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $game_wins
     */
    public static function setGameWins(string $player, int $game_wins): void {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_wins = ´" . $game_wins . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @param array $kits
     */
    public static function setKits(string $player, array $kits): void {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_kits SET kits_unlocked = ´" . implode(":", $kits) . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return array
     */
    public static function getKits(string $player): array {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT kits_unlocked FROM flagwars_kits WHERE player = ´" . $player . "´");
        $mysql->close();
        $kits = explode(":", $statement->fetch_array()[0]);
        $result = [];
        foreach ($kits as $kit) {
            if(strlen($kit) > 2) $result[] = $result;
        }
        return $result;
    }

    /**
     * @param string $player
     * @param string $kit
     */
    public static function setKit(string $player, string $kit): void {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_kits SET kit = ´" . $kit . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return string
     */
    public static function getKit(string $player): string {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT kit FROM flagwars_kits WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }
}