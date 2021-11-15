<?php

namespace matze\flagwars\entity;

use matze\flagwars\utils\Settings;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\sound\PopSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;
use ryzerbe\core\util\Vector3Utils;

class SpawnerEntity extends ItemEntity implements ChunkLoader {

    /**
     * SpawnerEntity constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }

    public function initEntity(): void {
        $nbt = $this->namedtag;
        $type = $nbt->getString("Type", "N/A");
        switch ($type) {
            case "iron": {
                $nbt->setString("Color", "§f");
                $nbt->setInt("Delay", Settings::$iron_spawn_delay[1]);
                $item = Item::get(ItemIds::IRON_INGOT)->setCustomName("§r§fIron");
                break;
            }
            case "gold": {
                $nbt->setString("Color", "§6");
                $nbt->setInt("Delay", Settings::$gold_spawn_delay[1]);
                $item = Item::get(ItemIds::GOLD_INGOT)->setCustomName("§r§6Gold");
                break;
            }
            default: {
                $nbt->setString("Color", "§a");
                $nbt->setInt("Delay", Settings::$bronze_spawn_delay[1]);
                $item = Item::get(ItemIds::BRICK)->setCustomName("§r§aBronze");
            }
        }
        $nbt->setString("ForcePosition", Vector3Utils::toString($this));
        $itemTag = $item->nbtSerialize();
        $itemTag->setName("Item");
        $nbt->setTag($itemTag);

        parent::initEntity();

        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
        $this->setMaxHealth(20);
        $this->setHealth(20);

        $this->initText();
    }

    /** @var int  */
    public $drag = 0;
    /** @var int  */
    public $gravity = 0;

    /** @var float  */
    public $width = 1;
    /** @var float  */
    public $height = 0.4;

    /** @var FloatingTextParticle|null */
    private ?FloatingTextParticle $floatingText = null;
    /** @var int  */
    private int $spawnerLevel = 1;

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool {
        $this->age = 0;

        $spawnDelay = $this->namedtag->getInt("Delay", 10);
        $color = $this->namedtag->getString("Color", "§a");
        $item = $this->getItem();

        $amount = $item->getCount();
        $this->setNameTag("§r§l" . $color  . $amount);

        if(($currentTick % $spawnDelay) === 0) {
            $item->setCount($item->getCount() + 1);
            $amount += 1;
        }

        if(($currentTick % 5) === 0) {
            //Hack: Because setImmobile() doesn`t work
            $this->teleport(Vector3Utils::fromString($this->namedtag->getString("ForcePosition")));//Do not go away my son ;(
        }

        $collidingEntities = 0;
        foreach ($this->getLevel()->getNearbyEntities($this->getBoundingBox()) as $player) {
            if(!$player instanceof Player) {
                continue;
            }
            if($player->isSpectator()) {
                continue;
            }
            $collidingEntities++;
            if($amount <= 0) {
                break;
            }
            $aItem = $this->getItem();
            $aItem->setCount(1);
            if($amount > 1) {
                $aItem->setCount($amount);
            }
            $player->getInventory()->addItem($aItem);
            $player->getLevel()->addSound(new PopSound($player, 1), [$player]);

            $item->setCount(1);
            $amount = 1;
        }
        if($collidingEntities > 0) {
            $item->setCount(0);
            return parent::onUpdate($currentTick);
        }
        return parent::onUpdate($currentTick);
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->namedtag->getString("Type", "N/A");
    }

    /**
     * @return int
     */
    public function getSpawnerLevel(): int {
        return $this->spawnerLevel;
    }

    public const UPGRADE_REASON_ALREADY_MAX_LEVEL = 0;
    public const UPGRADE_REASON_CAN_UPGRADE = 1;
    public const UPGRADE_REASON_NOT_ENOUGH_ITEMS = 2;

    /**
     * @param Player $player
     * @return int
     */
    public function canUpgrade(Player $player): int {
        if($this->spawnerLevel >= 3) return self::UPGRADE_REASON_ALREADY_MAX_LEVEL;
        $cost = $this->getUpgradeCost($this->spawnerLevel + 1);
        if($cost === -1) return self::UPGRADE_REASON_ALREADY_MAX_LEVEL;

        $contents = [];
        foreach ($player->getInventory()->getContents() as $item) {
            $sItem = implode(":", [$item->getId(), $item->getDamage()]);
            if(!isset($contents[$sItem])) $contents[$sItem] = 0;
            $contents[$sItem] += $item->getCount();
        }
        $item = $this->getItem();
        $sItem = implode(":", [$item->getId(), $item->getDamage()]);
        if(!isset($contents[$sItem])) return self::UPGRADE_REASON_NOT_ENOUGH_ITEMS;
        if($contents[$sItem] >= $cost) {
            return self::UPGRADE_REASON_CAN_UPGRADE;
        }
        return self::UPGRADE_REASON_NOT_ENOUGH_ITEMS;
    }

    /**
     * @return int - Cost for upgrade
     */
    public function upgrade(): int {
        $this->spawnerLevel++;
        $nbt = $this->namedtag;
        switch ($this->getType()) {
            case "iron": {
                $nbt->setInt("Delay", Settings::$iron_spawn_delay[$this->spawnerLevel]);
                break;
            }
            case "gold": {
                $nbt->setInt("Delay", Settings::$gold_spawn_delay[$this->spawnerLevel]);
                break;
            }
            default: {
                $nbt->setInt("Delay", Settings::$bronze_spawn_delay[$this->spawnerLevel]);
            }
        }
        $this->initText();
        return $this->getUpgradeCost();
    }

    /**
     * @param int|null $spawnerLevel
     * @return int
     */
    public function getUpgradeCost(?int $spawnerLevel = null): int {
        if(is_null($spawnerLevel)) {
            $spawnerLevel = $this->spawnerLevel;
        }
        return match ($this->getType()) {
            "iron" => Settings::$iron_upgrade_cost[$spawnerLevel] ?? -1,
            "gold" => Settings::$gold_upgrade_cost[$spawnerLevel] ?? -1,
            default => Settings::$bronze_upgrade_cost[$spawnerLevel] ?? -1,
        };
    }

    public function initText(): void {
        $floatingText = $this->floatingText;
        if(is_null($floatingText)) {
            $floatingText = new FloatingTextParticle($this->add(0, 1.5), "");
        }
        $color = $this->namedtag->getString("Color", "§a");
        $floatingText->setText("§r" . $color . strtoupper($this->getType()) . " SPAWNER §eLVL " . $this->spawnerLevel . ($this->spawnerLevel >= 3 ? " §7(§8MAX§7)": "\n§r§fClick to upgrade"));

        $this->floatingText = $floatingText;
        $this->getLevel()->addParticle($floatingText, Server::getInstance()->getOnlinePlayers());
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function canCollideWith(Entity $entity): bool {
        return false;
    }

    /**
     * @return int
     */
    public function getPickupDelay(): int {
        return -1;
    }

    /**
     * @return bool
     */
    public function isFireProof(): bool {
        return true;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void {
        $source->setCancelled();
    }

    /**
     * @return int
     */
    public function getLoaderId(): int {
        return spl_object_id($this);
    }

    /**
     * @return bool
     */
    public function isLoaderActive(): bool {
        return ($this->isAlive() && !($this->isClosed()));
    }

    /**
     * @return Block
     */
    public function getBlock(): Block {
        return $this->getLevel()->getBlock($this->subtract(0, 1));
    }

    public function onChunkChanged(Chunk $chunk): void {}
    public function onChunkLoaded(Chunk $chunk): void {}
    public function onChunkUnloaded(Chunk $chunk): void{}
    public function onChunkPopulated(Chunk $chunk): void {}
    public function onBlockChanged(Vector3 $block): void {}
}