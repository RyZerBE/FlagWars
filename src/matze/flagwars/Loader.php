<?php

namespace matze\flagwars;

use matze\flagwars\command\SetupCommand;
use matze\flagwars\game\GameManager;
use matze\flagwars\listener\BlockBreakListener;
use matze\flagwars\listener\BlockPlaceListener;
use matze\flagwars\listener\PlayerInteractListener;
use matze\flagwars\listener\PlayerJoinListener;
use matze\flagwars\listener\PlayerLoginListener;
use matze\flagwars\listener\PlayerQuitListener;
use matze\flagwars\scheduler\GameUpdateTask;
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
        $this->initCommands();

        $this->getScheduler()->scheduleRepeatingTask(new GameUpdateTask(), 20);
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
            new PlayerLoginListener(),
            new PlayerQuitListener(),
            new PlayerInteractListener(),
            new BlockPlaceListener(),
            new BlockBreakListener()
        ];
        foreach ($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
        }
    }

    private function initCommands(): void {
        $commands = [
            new SetupCommand("setup")
        ];
        foreach ($commands as $command) {
            Server::getInstance()->getCommandMap()->register("FlagWars", $command);
        }
    }
}