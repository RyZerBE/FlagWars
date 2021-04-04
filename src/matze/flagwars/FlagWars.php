<?php

namespace matze\flagwars;

use matze\flagwars\player\FlagWarsPlayer;
use matze\flagwars\provider\FlagWarsProvider;
use matze\flagwars\utils\InstantiableTrait;
use pocketmine\Player;

class FlagWars {
    use InstantiableTrait;

    public const PREFIX = "§r§bFlag§3Wars§8 | §7";

    /** @var FlagWarsProvider */
    private static $provider;

    /**
     * MarioParty constructor.
     */
    public function __construct() {
        self::$provider = new FlagWarsProvider();
    }

    /**
     * @return FlagWarsProvider
     */
    public static function getProvider(): FlagWarsProvider {
        return self::$provider;
    }

    /**
     * @return Loader
     */
    public static function getLoader(): Loader {
        return Loader::getInstance();
    }

    /** @var array  */
    private static $players = [];

    /**
     * @param Player $player
     */
    public static function addPlayer(Player $player): void {
        self::$players[$player->getName()] = new FlagWarsPlayer($player);
    }

    /**
     * @param Player $player
     * @return FlagWarsPlayer|null
     */
    public static function getPlayer(Player $player): ?FlagWarsPlayer {
        if(!isset(self::$players[$player->getName()])) {
            return null;
        }
        return self::$players[$player->getName()];
    }

    /**
     * @param Player $player
     */
    public static function removePlayer(Player $player): void {
        if(!isset(self::$players[$player->getName()])) return;
        unset(self::$players[$player->getName()]);
    }

    /**
     * @return array
     */
    public static function getPlayers(): array {
        return self::$players;
    }
}