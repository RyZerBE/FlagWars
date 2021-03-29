<?php

namespace matze\flagwars;

use matze\flagwars\game\GameManager;
use matze\flagwars\listener\PlayerJoinListener;
use matze\flagwars\listener\PlayerLoginListener;
use matze\flagwars\utils\Settings;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Loader extends PluginBase {

    /** @var Loader|null */
    private static $instance = null;

    public function onEnable(): void {
        self::$instance = $this;

        new Settings();

        FlagWars::getInstance();
        GameManager::getInstance();

        $this->initListener();
    }

    /**
     * @return Loader|null
     */
    public static function getInstance(): ?Loader {
        return self::$instance;
    }

    private function initListener(): void {
        $listeners = [
            new PlayerJoinListener(),
            new PlayerLoginListener()
        ];
        foreach ($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
        }
    }
}