<?php

namespace matze\flagwars\listener;

use BauboLP\Cloud\CloudBridge;
use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\entity\SpawnerEntity;
use matze\flagwars\FlagWars;
use matze\flagwars\forms\Forms;
use matze\flagwars\game\GameManager;
use matze\flagwars\provider\FlagWarsProvider;
use matze\flagwars\utils\Settings;
use pocketmine\block\BlockIds;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\util\ItemUtils;

class PlayerInteractListener implements Listener {

    /** @var array  */
    private array $cooldown = [];

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
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
                case "kit_selection":
                {
                    Forms::getSelectKitForm()->open($player);
                    break;
                }
                case "team_selection":
                {
                    Forms::getSelectTeamForm()->open($player);
                    break;
                }
                case "map_selection":
                {
                    if ($game->getCountdown() <= 5) {
                        $player->getLevel()->addSound(new ClickSound($player), [$player]);
                        break;
                    }
                    Forms::getSelectMapForm()->open($player);
                    break;
                }
                case "info":
                    $form = new SimpleForm(null);
                    $form->setTitle(FlagWars::PREFIX.TextFormat::GREEN."Info");
                    $form->setContent(LanguageProvider::getMessageContainer('info-fw', $player->getName()));
                    $form->sendToPlayer($player);
                    break;
                case "quit":
                {
                    CloudBridge::getCloudProvider()->dispatchProxyCommand($player->getName(), "hub");
                    break;
                }
                case "player_teleporter":
                {
                    if($player->hasItemCooldown($item)) return;
                    $player->resetItemCooldown($item, 20);

                    $form = new SimpleForm(function(Player $player, $data): void{
                        if($data === null) return;

                        $tpPlayer = $player->getServer()->getPlayerExact($data);
                        if($tpPlayer === null) return;
                        if($tpPlayer->isSpectator()) return;

                        $player->teleport($tpPlayer);
                    });

                    foreach(FlagWars::getPlayers() as $gamePlayer) {
                        if($gamePlayer->getPlayer()->isSpectator()) continue;
                        $form->addButton($gamePlayer->getPlayer()->getDisplayName(), -1, "", $gamePlayer->getPlayer()->getName());
                    }

                    $form->sendToPlayer($player);
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

                                $item = clone $spawner->getItem();
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
                            $form->addButton(TextFormat::GREEN.TextFormat::BOLD."✔ UPGRADE");
                            $form->sendToPlayer($player);
                        } else {
                            switch ($upgrade) {
                                case SpawnerEntity::UPGRADE_REASON_ALREADY_MAX_LEVEL: {
                                    $player->sendTip(LanguageProvider::getMessageContainer('fw-spawner-max-level', $player->getName()));
                                    break;
                                }
                                case SpawnerEntity::UPGRADE_REASON_NOT_ENOUGH_ITEMS: {
                                    $player->sendTip(LanguageProvider::getMessageContainer('fw-not-enough-resources', $player->getName(), ["#needed" => $spawner->getUpgradeCost($spawner->getSpawnerLevel() + 1)]));
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
        }else if($action === PlayerInteractEvent::RIGHT_CLICK_AIR) {
            switch ($item->getId()) {
                case ItemIds::GHAST_TEAR:
                    $player->getInventory()->removeItem(Item::get(ItemIds::GHAST_TEAR));
                    $player->knockBack($player, 0, $player->getDirectionVector()->getX(), $player->getDirectionVector()->getZ(), 1.6);
                    break;
                case BlockIds::LEVER:
                    FlagWarsProvider::createWall($player);
                    break;
                case ItemIds::BLAZE_ROD:
                    FlagWarsProvider::createSafetyPlatform($player);
                    break;
            }
        }
    }
}
