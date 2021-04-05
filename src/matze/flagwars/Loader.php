<?php

namespace matze\flagwars;

use matze\flagwars\command\SetupCommand;
use matze\flagwars\command\StartCommand;
use matze\flagwars\entity\FlagEntity;
use matze\flagwars\entity\ShopEntity;
use matze\flagwars\entity\SpawnerEntity;
use matze\flagwars\game\GameManager;
use matze\flagwars\listener\BlockBreakListener;
use matze\flagwars\listener\BlockBurnListener;
use matze\flagwars\listener\BlockFormListener;
use matze\flagwars\listener\BlockPlaceListener;
use matze\flagwars\listener\CraftItemListener;
use matze\flagwars\listener\EntityDamageListener;
use matze\flagwars\listener\EntityExplodeListener;
use matze\flagwars\listener\InventoryPickUpListener;
use matze\flagwars\listener\InventoryTransactionListener;
use matze\flagwars\listener\LeavesDecayListener;
use matze\flagwars\listener\PlayerBedEnterListener;
use matze\flagwars\listener\PlayerChatListener;
use matze\flagwars\listener\PlayerDeathListener;
use matze\flagwars\listener\PlayerDropItemListener;
use matze\flagwars\listener\PlayerExhaustListener;
use matze\flagwars\listener\PlayerInteractEntityListener;
use matze\flagwars\listener\PlayerInteractListener;
use matze\flagwars\listener\PlayerJoinListener;
use matze\flagwars\listener\PlayerLoginListener;
use matze\flagwars\listener\PlayerMoveListener;
use matze\flagwars\listener\PlayerQuitListener;
use matze\flagwars\scheduler\GameUpdateTask;
use matze\flagwars\utils\Settings;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\Entity;
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
        $this->initEntities();

        $this->getScheduler()->scheduleRepeatingTask(new GameUpdateTask(), 1);

        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
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
            new BlockBreakListener(),
            new EntityDamageListener(),
            new PlayerExhaustListener(),
            new PlayerDeathListener(),
            new PlayerDropItemListener(),
            new PlayerMoveListener(),
            new CraftItemListener(),
            new InventoryTransactionListener(),
            new EntityExplodeListener(),
            new LeavesDecayListener(),
            new BlockFormListener(),
            new BlockBurnListener(),
            new PlayerBedEnterListener(),
            new InventoryPickUpListener(),
            new PlayerChatListener(),
            new PlayerInteractEntityListener()
        ];
        foreach ($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
        }
    }

    private function initCommands(): void {
        $commands = [
            new SetupCommand("setup"),
            new StartCommand("start")
        ];
        foreach ($commands as $command) {
            Server::getInstance()->getCommandMap()->register("FlagWars", $command);
        }
    }

    private function initEntities(): void {
        $entities = [
            SpawnerEntity::class,
            ShopEntity::class,
            FlagEntity::class
        ];
        foreach ($entities as $entity) {
            Entity::registerEntity($entity, true);
        }
    }
}