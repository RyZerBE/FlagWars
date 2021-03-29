<?php

namespace matze\flagwars\command;

use matze\flagwars\game\GameManager;
use matze\flagwars\game\Map;
use matze\flagwars\utils\ItemUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

class SetupCommand extends Command {

    /**
     * SetupCommand constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        parent::__construct($name, "Setup Command");
        $this->setPermission("setup.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) {
            return;
        }
        if(!$this->testPermissionSilent($sender)) {
            return;
        }
        if(!isset($args[1]) || empty($args[0]) || empty($args[1])) {
            $sender->sendMessage("§8» §r§7/setup [Level] [Creator]");
            return;
        }
        $level = $args[0];
        $creator = $args[1];

        if(!is_dir("worlds/" . $level)) {
            $sender->sendMessage("§8» §r§7/setup [Level] [Creator]");
            return;
        }
        Server::getInstance()->loadLevel($level);
        $level = Server::getInstance()->getLevelByName($level);

        if($sender->getLevel()->getName() !== $level->getName()) {
            $sender->teleport($level->getSpawnLocation());
        }
        $sender->sendMessage("§8» §r§7Setup Items received.");
        $sender->setGamemode(1);

        $game = GameManager::getInstance();
        $map = $game->getMapByName($level->getFolderName());
        if(is_null($map)) {
            $map = new Map($level->getFolderName());
            $game->registerMap($map);
        }

        $settings = $map->getSettings();
        $settings->set("Creator", $creator);
        $settings->save();

        $sender->getInventory()->setContents([
            0 => ItemUtils::addItemTag(Item::get(Item::BANNER)->setCustomName("§r§aFlaggen Position \n§7[§8Place§7]"), "flag_position", "map_setup"),
            1 => ItemUtils::addItemTag(Item::get(Item::WOOL, mt_rand(0, 15))->setCustomName("§r§aTeam Spawn Positions \n§7[§8Place§7]"), "team_spawn_positions", "map_setup"),
            2 => ItemUtils::addItemTag(Item::get(Item::MOB_SPAWNER)->setCustomName("§r§aSpawner Positions \n§7[§8Place§7]"), "spawner_positions", "map_setup"),
            3 => ItemUtils::addItemTag(Item::get(Item::CHEST)->setCustomName("§r§aShop Positions \n§7[§8Place§7]"), "shop_positions", "map_setup")
        ]);
    }
}