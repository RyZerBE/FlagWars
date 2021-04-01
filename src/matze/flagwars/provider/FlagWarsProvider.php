<?php

namespace matze\flagwars\provider;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\shop\ShopManager;
use matze\flagwars\utils\Vector3Utils;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Config;

class FlagWarsProvider
{

    /** @var string */
    private static $host;
    /** @var string */
    private static $user;
    /** @var string */
    private static $password;
    /** @var string */
    private static $database;

    /**
     * FlagWarsProvider constructor.
     */
    public function __construct()
    {
        $mysqlSettings = new Config("mysql.yml");
        self::$host = $mysqlSettings->get("Host");
        self::$user = $mysqlSettings->get("User");
        self::$password = $mysqlSettings->get("Password");
        self::$database = $mysqlSettings->get("Database");
    }

    /**
     * @param string $player
     */
    public static function checkRegistered(string $player): void
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT * FROM flagwars_stats WHERE player = ´" . $player . "´");
        if (($statement->num_rows > 0)) {
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
    public static function getKills(string $player): int
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_kills FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $kills
     */
    public static function setKills(string $player, int $kills): void
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_kills = ´" . $kills . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getDeaths(string $player): int
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_deaths FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $deaths
     */
    public static function setDeaths(string $player, int $deaths): void
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_deaths = ´" . $deaths . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getGamePlays(string $player): int
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_plays FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $game_plays
     */
    public static function setGamePlays(string $player, int $game_plays): void
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_plays = ´" . $game_plays . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getGameWins(string $player): int
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT game_wins FROM flagwars_stats WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param string $player
     * @param int $game_wins
     */
    public static function setGameWins(string $player, int $game_wins): void
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_stats SET game_wins = ´" . $game_wins . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @param array $kits
     */
    public static function setKits(string $player, array $kits): void
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_kits SET kits_unlocked = ´" . implode(":", $kits) . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return array
     */
    public static function getKits(string $player): array
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT kits_unlocked FROM flagwars_kits WHERE player = ´" . $player . "´");
        $mysql->close();
        $kits = explode(":", $statement->fetch_array()[0]);
        $result = [];
        foreach ($kits as $kit) {
            if (strlen($kit) > 2) $result[] = $result;
        }
        return $result;
    }

    /**
     * @param string $player
     * @param string $kit
     */
    public static function setKit(string $player, string $kit): void
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $mysql->query("UPDATE flagwars_kits SET kit = ´" . $kit . "´ WHERE player = ´" . $player . "´");
        $mysql->close();
    }

    /**
     * @param string $player
     * @return string
     */
    public static function getKit(string $player): string
    {
        $mysql = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
        $statement = $mysql->query("SELECT kit FROM flagwars_kits WHERE player = ´" . $player . "´");
        $mysql->close();
        return $statement->fetch_array()[0];
    }

    /**
     * @param \pocketmine\Player $player
     */
    public static function createWall(Player $player): void
    {
        $fwPlayer = FlagWars::getPlayer($player);
        if ($fwPlayer === null) return;

        $pos = [];
        $player->getInventory()->removeItem(Item::get(Item::LEVER, 0, 1));
        $direction = $player->getDirection();
        $arena = GameManager::getInstance();

        switch ($direction) {
            case Entity::SIDE_WEST:
            case Entity::SIDE_EAST:
                //TODO: USELESS
                break;
            case Entity::SIDE_NORTH:
                $pos = [];
                $pos[] = $player->asVector3()->add(-2);
                $pos[] = $player->asVector3()->add(-2, 0, -1);
                $pos[] = $player->asVector3()->add(-2, 0, 1);

                $pos[] = $player->asVector3()->add(-2, 1);
                $pos[] = $player->asVector3()->add(-2, 1, -1);
                $pos[] = $player->asVector3()->add(-2, 1, 1);

                $pos[] = $player->asVector3()->add(-2, 2);
                $pos[] = $player->asVector3()->add(-2, 2, -1);
                $pos[] = $player->asVector3()->add(-2, 2, 1);
                break;
            case Entity::SIDE_SOUTH:
                $pos[] = $player->asVector3()->add(0, 0, -2);
                $pos[] = $player->asVector3()->add(1, 0, -2);
                $pos[] = $player->asVector3()->add(-1, 0, -2);

                $pos[] = $player->asVector3()->add(1, 1, -2);
                $pos[] = $player->asVector3()->add(-1, 1, -2);
                $pos[] = $player->asVector3()->add(0, 1, -2);

                $pos[] = $player->asVector3()->add(1, 2, -2);
                $pos[] = $player->asVector3()->add(-1, 2, -2);
                $pos[] = $player->asVector3()->add(0, 2, -2);
                break;
            case Entity::SIDE_UP:
                $pos[] = $player->asVector3()->add(0, 0, +2);
                $pos[] = $player->asVector3()->add(1, 0, +2);
                $pos[] = $player->asVector3()->add(-1, 0, +2);

                $pos[] = $player->asVector3()->add(1, 1, +2);
                $pos[] = $player->asVector3()->add(-1, 1, +2);
                $pos[] = $player->asVector3()->add(0, 1, +2);

                $pos[] = $player->asVector3()->add(1, 2, +2);
                $pos[] = $player->asVector3()->add(-1, 2, +2);
                $pos[] = $player->asVector3()->add(0, 2, +2);
                break;
            case Entity::SIDE_DOWN:
                $pos[] = $player->asVector3()->add(+2);
                $pos[] = $player->asVector3()->add(+2, 0, -1);
                $pos[] = $player->asVector3()->add(+2, 0, 1);

                $pos[] = $player->asVector3()->add(+2, 1);
                $pos[] = $player->asVector3()->add(+2, 1, -1);
                $pos[] = $player->asVector3()->add(+2, 1, 1);

                $pos[] = $player->asVector3()->add(+2, 2);
                $pos[] = $player->asVector3()->add(+2, 2, -1);
                $pos[] = $player->asVector3()->add(+2, 2, 1);
                break;
        }


        $level = $player->getLevel();
        /** @var Vector3 $position */
        foreach ($pos as $position) {
            $block = $level->getBlock($position);
            if ($block->getId() === Block::AIR) {
                $level->setBlock($position, Block::get(Block::WOOL, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor())));
                $arena->addBlock($block);
            }
        }
    }

    public static function createSafetyPlatform(Player $player): void
    {
        $fwPlayer = FlagWars::getPlayer($player);
        if ($fwPlayer === null) return;

        $player->getInventory()->removeItem(Item::get(Item::BLAZE_ROD, 0, 1));
        $arena = GameManager::getInstance();
        $playerVec = $player->asVector3()->add(0, 0.5);
        for ($x = -1; $x <= 1; $x++) {
            for ($z = -1; $z <= 1; $z++) {
                $vec = $player->add($x, -1, $z);
                $block = $player->getLevel()->getBlockAt($vec->x, $vec->y, $vec->z);
                if ($block->getId() === Block::AIR) {
                    $player->getLevel()->setBlock($vec, Block::get(Block::WOOL, ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor())));
                    $arena->addBlock($block);
                }
            }
        }
        $player->teleport($playerVec);
    }
}