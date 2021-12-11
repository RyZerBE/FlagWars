<?php

namespace matze\flagwars\listener;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\shop\ShopManager;
use matze\flagwars\utils\Settings;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;

class EntityDamageListener implements Listener {

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event): void {
        $player = $event->getEntity();
        if(!$player instanceof Player) return;
        $game = GameManager::getInstance();

        if(!$game->isIngame()) {
            if($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
                $player->teleport(Settings::$waiting_lobby_location);
            }
            $event->setCancelled();
            return;
        }

        if($event->getFinalDamage() >= $player->getHealth()) {
            $event->setCancelled();

            $ev = new PlayerDeathEvent($player, []);
            $ev->call();
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function attack(EntityDamageByEntityEvent $event): void {
        $player = $event->getEntity();
        $damager = $event->getDamager();

        if(!$player instanceof Player) return;
        $fwPlayer = FlagWars::getPlayer($player);
        if($fwPlayer === null) {
            $event->setCancelled();
            return;
        }

        if(!$damager instanceof Player) {
            if (!is_null($damager->getOwningEntity()))
                $damager = $damager->getOwningEntity();

            if ($damager instanceof PrimedTNT && $player instanceof Player) {
                $team = $damager->namedtag->getString("Team", 8);
                if (ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor()) == $team) {
                    $event->setCancelled();
                    return;
                }
            }

            if (!$damager instanceof Player)
                return;
        }
        $game = GameManager::getInstance();
        $fwDamager = FlagWars::getPlayer($damager);

        if(!$game->isIngame()) {
            $event->setCancelled();
            $event->setKnockBack(0);
            return;
        }
        if($fwDamager->getTeam()->isPlayer($player)) {
            $event->setCancelled();
            $event->setKnockBack(0);
            return;
        }
        $fwPlayer->setLastDamager($damager);
    }
}