<?php

namespace matze\flagwars\listener;

use baubolp\core\provider\LanguageProvider;
use matze\flagwars\FlagWars;
use matze\flagwars\utils\ItemUtils;
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

        $killer = $fwPlayer->getLastDamager();
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
                foreach ($kit->getItems($player) as $item) $player->getInventory()->addItem(ItemUtils::addItemTag($item, "kit_item", "kit_item"));
            }
        }

        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if(is_null($killer)) {
                $onlinePlayer->sendMessage(FlagWars::PREFIX . LanguageProvider::getMessageContainer('player-fell-in-void', $onlinePlayer->getName(), ['#playername' => $fwPlayer->getPlayer()->getDisplayName()]));
            } else {
                $onlinePlayer->sendMessage(FlagWars::PREFIX.LanguageProvider::getMessageContainer('player-killed-by-player', $onlinePlayer->getName(), ["#killername" => $killer->getDisplayName(), '#playername' => $fwPlayer->getPlayer()->getDisplayName()]));
            }
        }
    }
}
