<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\TaskExecuter;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Server;

class PlayerDeathListener implements Listener {

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $game = GameManager::getInstance();

        $fwPlayer->reset();
        $fwPlayer->setHasFlag(false);

        //Weird fall damage bug fix
        TaskExecuter::submitTask(1, function (int $tick) use ($player, $fwPlayer): void {
            if(!$player->isConnected()) return;
            $player->teleport($fwPlayer->getTeam()->getSpawnLocation());
        });

        $kit = $fwPlayer->getKit();
        if(!is_null($kit)) {
            if($kit->getItemsOnRespawn()) {
                foreach ($kit->getItems($player) as $item) $player->getInventory()->addItem($item);
            }
        }

        $killer = $fwPlayer->getLastDamager();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->sendMessage($player->getName() . (is_null($killer) ? " died." : " was killed by " . $killer->getName()));//todo: message
        }
    }
}