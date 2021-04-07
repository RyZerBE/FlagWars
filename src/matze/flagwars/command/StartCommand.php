<?php


namespace matze\flagwars\command;


use matze\flagwars\game\GameManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class StartCommand extends Command
{

    public function __construct(string $commandName)
    {
        parent::__construct($commandName, "Start the game", "", []);
        $this->setPermission("game.start");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;

        $game = GameManager::getInstance();
        if($game->getState() != $game::STATE_WAITING && $game->getCountdown() < 5) return;
        $game->setCountdown(5);
    }
}