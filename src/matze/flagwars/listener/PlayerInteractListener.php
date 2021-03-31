<?php

namespace matze\flagwars\listener;

use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\entity\SpawnerEntity;
use matze\flagwars\FlagWars;
use matze\flagwars\forms\Forms;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\ItemUtils;
use matze\flagwars\utils\Settings;
use matze\flagwars\utils\Vector3Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\Player;
use pocketmine\Server;

class PlayerInteractListener implements Listener {

    /** @var array  */
    private $cooldown = [];

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $item = $event->getItem();
        $action = $event->getAction();
        $block = $event->getBlock();
        $game = GameManager::getInstance();
        $level = $player->getLevel();
        $name = $player->getName();

        if(!isset($this->cooldown[$name])) {
            $this->cooldown[$name] = 0;
        }

        if(ItemUtils::hasItemTag($item, "function") && !$player->hasItemCooldown($item)) {
            $player->resetItemCooldown($item, 10);
            $event->setCancelled();
            switch (ItemUtils::getItemTag($item, "function")) {
                case "kit_selection": {
                    Forms::getSelectKitForm()->open($player);
                    break;
                }
                case "team_selection": {
                    Forms::getSelectTeamForm()->open($player);
                    break;
                }
                case "map_selection": {
                    if($game->getCountdown() <= 5) {
                        $player->getLevel()->addSound(new ClickSound($player), [$player]);
                        break;
                    }
                    Forms::getSelectMapForm()->open($player);
                    break;
                }
                case "quit": {
                    //CloudBridge::getCloudProvider()->dispatchProxyCommand($player->getName(), "hub");//todo
                    break;
                }
            }
        }

        if(!$game->isIngame()) {
            if($player->isCreative(true)) return;
            $event->setCancelled();
            return;
        }
        if($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if(Server::getInstance()->getTick() > $this->cooldown[$name]) {
                $this->cooldown[$name] = Server::getInstance()->getTick() + 10;
                $spawner = $level->getNearestEntity($block, 10, SpawnerEntity::class);
                if($spawner instanceof SpawnerEntity) {
                    $spawnerBlock = $spawner->getBlock();
                    if($spawnerBlock->floor()->equals($block->floor())) {
                        $upgrade = $spawner->canUpgrade($player);
                        if($upgrade === SpawnerEntity::UPGRADE_REASON_CAN_UPGRADE) {
                            $form = new SimpleForm(function (Player $player, $data) use ($spawner): void {
                                if(is_null($data)) return;
                                if($spawner->canUpgrade($player) !== SpawnerEntity::UPGRADE_REASON_CAN_UPGRADE) {
                                    $player->getLevel()->addSound(new AnvilFallSound($player), [$player]);
                                    return;
                                }
                                $fwPlayer = FlagWars::getPlayer($player);
                                $fwPlayer->playSound("random.levelup");
                                $cost = $spawner->upgrade();

                                $item = $spawner->getItem();
                                $item->setCount($cost);

                                foreach ($player->getInventory()->getContents() as $slot => $content) {
                                    if(!$content->equals($item, true, false)) {
                                        continue;
                                    }
                                    $count = $content->getCount();
                                    $player->getInventory()->setItem($slot, $content->setCount($count - $item->getCount()));
                                    $item->setCount($item->getCount() - $count);
                                    if($item->getCount() <= 0) break;
                                }
                            });
                            $form->setTitle("§r§l§f" . $spawner->getType() . " Spawner");
                            $form->setContent("§r§fCost§7: §f" . $spawner->getUpgradeCost($spawner->getSpawnerLevel() + 1) . " " . $spawner->getType());
                            $form->addButton("§r§7§lUPGRADE");
                            $form->sendToPlayer($player);
                        } else {
                            switch ($upgrade) {
                                case SpawnerEntity::UPGRADE_REASON_ALREADY_MAX_LEVEL: {
                                    $player->sendTip("Already maxed");//todo: tip
                                    break;
                                }
                                case SpawnerEntity::UPGRADE_REASON_NOT_ENOUGH_ITEMS: {
                                    $player->sendTip("Not enough items. Needed: " . $spawner->getUpgradeCost($spawner->getSpawnerLevel() + 1));//todo: tip
                                    break;
                                }
                            }
                            $player->getLevel()->addSound(new AnvilFallSound($player), [$player]);
                        }
                    }
                }
            }

            if(!$player->isCreative(true)) {
                if(in_array($block->getId(), Settings::$notInteractAbleBlocks)) {
                    $event->setCancelled();
                }
            }
        }
    }
}