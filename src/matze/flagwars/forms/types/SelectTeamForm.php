<?php

namespace matze\flagwars\forms\types;

use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\FlagWars;
use matze\flagwars\forms\Form;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\Settings;
use pocketmine\Player;
use ryzerbe\core\language\LanguageProvider;

class SelectTeamForm extends Form {

    /**
     * @param Player $player
     * @param int $window
     * @param array $extraData
     */
    public function open(Player $player, int $window = -1, array $extraData = []): void {
        $game = GameManager::getInstance();
        $form = new SimpleForm(function (Player $player, $data): void {
            if(is_null($data)) return;
            $fwPlayer = FlagWars::getPlayer($player);
            $game = GameManager::getInstance();
            $team = $game->getTeam($data);

            if($game->isIngame()) return;

            if($team->isPlayer($player)) return;
            if($team->isFull()) {
                $player->sendMessage(FlagWars::PREFIX.LanguageProvider::getMessageContainer("team-full", $player->getName(), ["#team" => $team->getColor().$team->getName()]));//todo: message
                return;
            }
            $team->addPlayer($player);
            $pTeam = $fwPlayer->getTeam();
            if(!is_null($pTeam)) {
                $pTeam->removePlayer($player);
            }
            $team->join($fwPlayer);
            $fwPlayer->playSound("random.orb");
        });
        $form->setTitle("§f§lFlagWars");
        foreach ($game->getTeams() as $team) {
            $form->addButton($team->getColor() . $team->getName() . "§7[" . ($team->isFull() ? "§c" : "§e") . count($team->getPlayers()) . "§e/§c" . Settings::$players_per_team . "§7]", -1, "", $team->getName());
        }
        $form->sendToPlayer($player);
    }
}