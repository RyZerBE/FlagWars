<?php


namespace matze\flagwars\forms\types;

use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\FlagWars;
use matze\flagwars\forms\Form;
use matze\flagwars\game\GameManager;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class KitDescriptionForm extends Form
{

    public function open(Player $player, int $window = -1, array $extraData = []): void
    {
        //TODO: KIT BOUGHT CHECK
        $form = new SimpleForm(function (Player $player, $data) use ($extraData): void {
            if(is_null($data)) return;
            $game = GameManager::getInstance();
            if($game->isIngame()) return;
            $fwPlayer = FlagWars::getPlayer($player);
            $fwPlayer->setKit(GameManager::getInstance()->getKit($extraData["name"]));
            $fwPlayer->playSound("random.orb");
        });
        $kit = GameManager::getInstance()->getKit($extraData["name"]);
        $form->setTitle(TextFormat::GOLD.TextFormat::BOLD.$extraData["name"]);
        $form->setContent($kit->getDescription());
        $form->addButton(TextFormat::GREEN.TextFormat::BOLD."✔ USE KIT", -1, "");
        $form->sendToPlayer($player);
    }
}