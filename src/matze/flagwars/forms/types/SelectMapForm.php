<?php

namespace matze\flagwars\forms\types;

use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\FlagWars;
use matze\flagwars\forms\Form;
use matze\flagwars\game\GameManager;
use matze\flagwars\game\Map;
use pocketmine\Player;

class SelectMapForm extends Form {

    /**
     * @param Player $player
     * @param int $window
     * @param array $extraData
     */
    public function open(Player $player, int $window = -1, array $extraData = []): void {
        $game = GameManager::getInstance();
        $fwPlayer = FlagWars::getPlayer($player);
        $form = new SimpleForm(function (Player $player, $data): void {
            if(is_null($data)) return;
            $game = GameManager::getInstance();
            $map = $game->getMapByName($data);
            if(is_null($map)) return;
            $fwPlayer = FlagWars::getPlayer($player);
            if($fwPlayer->getMapVote() == $map->getName()) {
                return;
            }
            $fwPlayer->setMapVote($map->getName());
            $fwPlayer->playSound("random.orb");
            $player->sendMessage("Map selected: " . $map->getName());//todo: message
        });
        $form->setTitle("§f§lFlagWars");
        foreach ($game->getMapPool() as $mapName => $map) {
            $form->addButton("§e" . $map->getName() . "\n§f" . $map->getCreator() . " §8| §c" . count(array_filter($game->getPlayers(), function (Player $player) use ($mapName): bool {
                return FlagWars::getPlayer($player)->getMapVote() === $mapName;
            })), -1, "", $mapName);
        }
        $form->sendToPlayer($player);
    }
}