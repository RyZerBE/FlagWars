<?php

namespace matze\flagwars\forms\types;

use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\FlagWars;
use matze\flagwars\forms\Form;
use matze\flagwars\game\GameManager;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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
            $game = GameManager::getInstance();
            if($game->isIngame()) return;
            $fwPlayer = FlagWars::getPlayer($player);
            if($fwPlayer === null) return;

            $form = new KitDescriptionForm();
            $form->open($player, -1, ["name" => $data, "unlocked" => (in_array($data, $fwPlayer->getUnlockedKits()) || $player->hasPermission("kits.free")) ? true : false]);
        });

        $fwPlayer = FlagWars::getPlayer($player);
        if($fwPlayer === null) return;

        $form->setTitle("§r§l§fFlagWars");
        $playerKit = $fwPlayer->getKit();

        if($playerKit === null)
            $playerKit = "#CoVid19";
        else
            $playerKit = $fwPlayer->getKit()->getName();
        foreach (GameManager::getInstance()->getKits() as $kit) {
            if($kit->getName() === $playerKit) {
                $info = TextFormat::AQUA . "AUSGEWÄHLT";
                $picture = "textures/ui/op";
            } else if($fwPlayer->boughtKit($kit)) {
                $info = TextFormat::GREEN."GEKAUFT";
                $picture = "textures/ui/confirm.png";
            } else {
                $info = TextFormat::GOLD.$kit->getPrice()." Coins";
                $picture = "textures/ui/realms_red_x.png";
            }

            $form->addButton(TextFormat::GRAY. $kit->getName()."\n".TextFormat::DARK_GRAY."(".$info.TextFormat::DARK_GRAY.")", 0, $picture, $kit->getName());
        }
        $form->sendToPlayer($player);
    }
}