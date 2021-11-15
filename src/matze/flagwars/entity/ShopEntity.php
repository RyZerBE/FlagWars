<?php

namespace matze\flagwars\entity;

use matze\flagwars\FlagWars;
use matze\flagwars\shop\categories\RushCategory;
use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;
use ryzerbe\core\util\Vector3Utils;

class ShopEntity extends Creature {

    /** @var int  */
    public const NETWORK_ID = self::VILLAGER;

    /** @var float  */
    public $width = 0.6;
    /** @var float  */
    public $height = 1.8;

    public function initEntity(): void {
        $this->namedtag->setString("ForcePosition", Vector3Utils::toString($this));
        parent::initEntity();

        $this->setNameTag("§r§a§lShop");
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Villager";
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool {
        if(($currentTick % 5) === 0) {
            //Hack: Because setImmobile() doesn`t work
            $this->teleport(Vector3Utils::fromString($this->namedtag->getString("ForcePosition")));//Do not go away my son ;(
        }

        $entity = $this->getLevel()->getNearestEntity($this, 40, Player::class);
        if(!is_null($entity)) {
            $this->lookAt($entity);
            $this->updateMovement();
        }
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void {
        $source->setCancelled();

        if($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof Player) {
                $fwPlayer = FlagWars::getPlayer($damager);
                if($fwPlayer === null) return;

                $fwPlayer->getShopMenu()->updateCategory(new RushCategory());
                $fwPlayer->getShopMenu()->open();
            }
        }
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function canCollideWith(Entity $entity): bool {
        return false;
    }

    /**
     * @return bool
     */
    public function isFireProof(): bool {
        return true;
    }
}