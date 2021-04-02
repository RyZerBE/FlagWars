<?php

namespace matze\flagwars\listener;

use matze\flagwars\game\GameManager;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;

class EntityExplodeListener implements Listener {

    /**
     * @param EntityExplodeEvent $event
     */
    public function onExplode(EntityExplodeEvent $event): void {
        $game = GameManager::getInstance();
        $blockList = [];
        if($game->isIngame()) {
            foreach ($event->getBlockList() as $block) {
                if($game->isBlock($block)) $blockList[] = $block;
            }
        }
        $event->setBlockList($blockList);
    }
}