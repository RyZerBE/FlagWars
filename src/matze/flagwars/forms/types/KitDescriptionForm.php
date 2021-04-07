<?php


namespace matze\flagwars\forms\types;

use BauboLP\Core\Provider\LanguageProvider;
use BauboLP\Core\Player\RyzerPlayerProvider;
use BauboLP\Core\Provider\CoinProvider;
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
        $bought = $extraData["unlocked"];
        $form = new SimpleForm(function (Player $player, $data) use ($extraData, $bought): void {
            if(is_null($data)) return;
            $game = GameManager::getInstance();
            if($game->isIngame()) return;
            $fwPlayer = FlagWars::getPlayer($player);
            $kit = GameManager::getInstance()->getKit($extraData["name"]);
            if(($ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($player->getName())) != null) {
                if($bought) {
                    $fwPlayer->setKit($kit);
                    $fwPlayer->playSound("random.orb");
                }else {
                    if(($ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($player->getName())) != null) {
                        $coins = $ryzerPlayer->getCoins();
                        if($coins >= $kit->getPrice()) {
                            $fwPlayer->addUnlockedKit($kit->getName());
                            $fwPlayer->setKit($kit);
                            $fwPlayer->playSound("random.orb");
                            CoinProvider::removeCoins($player->getName(), $kit->getPrice());
                        }else {
                            $fwPlayer->playSound("note.bass");
                        }
                    }
                }
            }
        });
        $kit = GameManager::getInstance()->getKit($extraData["name"]);
        $form->setTitle(TextFormat::GOLD.TextFormat::BOLD.$extraData["name"]);

        if($bought) {
            $form->setContent($kit->getDescription());
            $form->addButton(TextFormat::GREEN.TextFormat::BOLD."✔ USE KIT", -1, "");
        }else {
            if(($ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($player->getName())) != null) {
                $coins = $ryzerPlayer->getCoins();
                if($coins >= $kit->getPrice()) {
                    $form->setContent($kit->getDescription()."\n".LanguageProvider::getMessageContainer('fw-enough-coins-to-buy', $player->getName()));
                    $form->addButton(TextFormat::GREEN.TextFormat::BOLD."✔ CLICK TO BUY", 0, "textures/ui/confirm.png");
                }else {
                    $form->setContent($kit->getDescription()."\n".LanguageProvider::getMessageContainer('fw-not-enough-coins-kit', $player->getName(), ['#coins' => $kit->getPrice() - $coins]));
                    $form->addButton(TextFormat::GREEN.TextFormat::BOLD."✘ CANNOT BUY", 0, "textures/ui/realms_red_x.png");
                }
            }
        }
        $form->sendToPlayer($player);
    }
}