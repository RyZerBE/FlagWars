<?php

namespace matze\flagwars\forms\types;

use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\FlagWars;
use matze\flagwars\forms\Form;
use matze\flagwars\game\GameManager;
use pocketmine\Player;

class SelectKitForm extends Form {

    /**
     * @param Player $player
     * @param int $window
     * @param array $extraData
     */
    public function open(Player $player, int $window = -1, array $extraData = []): void {
        //Temporary
        $form = new SimpleForm(function (Player $player, $data): void {
            if(is_null($data)) return;
            $fwPlayer = FlagWars::getPlayer($player);
            $fwPlayer->setKit(GameManager::getInstance()->getKit($data));
            $fwPlayer->playSound("random.orb");
        });
        $form->setTitle("§r§l§fFlagWars");
        foreach (GameManager::getInstance()->getKits() as $kit) {
            $form->addButton("§r§7" . $kit->getName(), -1, "", $kit->getName());
        }
        $form->sendToPlayer($player);
    }
}