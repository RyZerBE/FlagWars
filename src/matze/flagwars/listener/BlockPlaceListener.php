<?php

namespace matze\flagwars\listener;

use jojoe77777\FormAPI\SimpleForm;
use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\utils\ItemUtils;
use matze\flagwars\utils\LocationUtils;
use matze\flagwars\utils\SetupUtils;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\level\Location;
use pocketmine\level\particle\DustParticle;
use pocketmine\Player;

class BlockPlaceListener implements Listener {

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $game = GameManager::getInstance();
        $player = $event->getPlayer();
        $fwPlayer = FlagWars::getPlayer($player);
        $block = $event->getBlock();
        $item = $event->getItem();

        if(ItemUtils::hasItemTag($item, "map_setup")) {
            $event->setCancelled();
            if($player->hasItemCooldown($item)) {
                return;
            }
            $player->resetItemCooldown($item, 20);
            $map = $game->getMapByName($player->getLevel()->getFolderName());
            if(!is_null($map)) {
                $settings = $map->getSettings();
                $position = $block->floor()->add(0.5, 0, 0.5);
                $positionString = LocationUtils::toString(new Location($position->x, $position->y, $position->z, SetupUtils::calculateYaw($player, $position), 0, $player->getLevel()));

                switch (ItemUtils::getItemTag($item, "map_setup")) {
                    case "flag_position": {
                        $settings->set("FlagLocation", $positionString);
                        $settings->save();

                        $player->sendMessage("§8» §r§7Flag location set. " . str_replace(":", ", ", $positionString));

                        for ($n = 1; $n <= 20; $n++) {
                            $player->getLevel()->addParticle(new DustParticle($position->add(0, $n / 10, 0), mt_rand(), mt_rand(), mt_rand(), mt_rand()));
                        }
                        break;
                    }
                    case "shop_positions": {
                        $shops = $settings->get("Shops", []);
                        $shops[] = $positionString;
                        $settings->set("Shops",$shops);
                        $settings->save();

                        $player->sendMessage("§8» §r§7Shop location set. " . str_replace(":", ", ", $positionString));
                        for ($n = 1; $n <= 20; $n++) {
                            $player->getLevel()->addParticle(new DustParticle($position->add(0, $n / 10, 0), mt_rand(), mt_rand(), mt_rand(), mt_rand()));
                        }
                        break;
                    }
                    case "team_spawn_positions": {
                        $form = new SimpleForm(function (Player $player, $data) use ($position, $settings, $positionString): void {
                            if(is_null($data)) return;

                            $settings->setNested("Spawns." . $data, $positionString);
                            $settings->save();

                            $player->sendMessage("§8» §r§7Team " . $data . " spawn position set. " . str_replace(":", ", ", $positionString));
                            for ($n = 1; $n <= 20; $n++) {
                                $player->getLevel()->addParticle(new DustParticle($position->add(0, $n / 10, 0), mt_rand(), mt_rand(), mt_rand(), mt_rand()));
                            }
                        });
                        $form->setTitle("§f§lTeam Setup");
                        foreach ($game->getTeams() as $teamName => $team) {
                            $form->addButton($team->getColor() . $teamName, -1, "", $teamName);
                        }
                        $form->sendToPlayer($player);
                        break;
                    }
                    case "spawner_positions": {
                        $form = new SimpleForm(function (Player $player, $data) use ($position, $settings, $positionString): void {
                            if(is_null($data)) return;
                            $spawner = $settings->get("Spawner", []);
                            $spawner[$positionString] = ["Type" => $data];

                            $settings->set("Spawner", $spawner);
                            $settings->save();

                            $player->sendMessage("§8» §r§7" . ucfirst($data) . " spawner position set. " . str_replace(":", ", ", $positionString));
                            for ($n = 1; $n <= 20; $n++) {
                                $player->getLevel()->addParticle(new DustParticle($position->add(0, $n / 10, 0), mt_rand(), mt_rand(), mt_rand(), mt_rand()));
                            }
                        });
                        $form->setTitle("§f§lSpawner Setup");
                        $form->addButton("§r§7Bronze Spawner", 0, "textures/items/brick", "bronze");
                        $form->addButton("§r§7Iron Spawner", 0, "textures/items/iron_ingot", "iron");
                        $form->addButton("§r§7Gold Spawner", 0, "textures/items/gold_ingot", "gold");
                        $form->sendToPlayer($player);
                        break;
                    }
                }
            }
        }
    }
}