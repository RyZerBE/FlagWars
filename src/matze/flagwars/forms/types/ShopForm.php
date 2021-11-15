<?php

namespace matze\flagwars\forms\types;

use matze\flagwars\forms\Form;
use muqsit\invmenu\InvMenu;
use pocketmine\Player;

class ShopForm extends Form {

    /**
     * @param Player $player
     * @param int $window
     * @param array $extraData
     */
    public function open(Player $player, int $window = -1, array $extraData = []): void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("§r§6Shop");

        $menu->send($player);
    }
}