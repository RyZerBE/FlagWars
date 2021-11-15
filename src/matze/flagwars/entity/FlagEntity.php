<?php

namespace matze\flagwars\entity;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\provider\FlagWarsProvider;
use matze\flagwars\utils\Settings;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\util\SkinUtils;

class FlagEntity extends Human {

    /**
     * FlagEntity constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt) {
        $this->skin = new Skin(
            "FlagEntity",
            SkinUtils::fromImage(Settings::SKIN_PATH . "Flag_White.png"),
            "",
            "geometry.Mobs.Zombie",
            file_get_contents(Settings::SKIN_PATH . "Flag.geo.json")
        );
        parent::__construct($level, $nbt);
    }

    public function initEntity(): void {
        parent::initEntity();
        $this->sendSkin();
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool {
        if($this->isClosed() || $this->isFlaggedForDespawn()) {
            return false;
        }
        $carrier = $this->getCarrier();
        $game = GameManager::getInstance();
        if(!$game->isFlag()) {
            $this->flagForDespawn();
            return parent::onUpdate($currentTick);
        }
        if(!is_null($carrier) && $carrier->isConnected()) {
            $fwCarrier = FlagWars::getPlayer($carrier);
            if(!$fwCarrier->hasFlag()) {
                $fwCarrier->getTeam()->setHasFlag(false);
                $this->setCarrier(null);

                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayer->sendTitle(TextFormat::YELLOW."⚠", LanguageProvider::getMessageContainer('flag-lost-subtitle', $onlinePlayer->getName()));
                    $onlinePlayer->sendMessage(FlagWars::PREFIX.LanguageProvider::getMessageContainer('flag-lost-subtitle', $onlinePlayer->getName()));
                    $onlinePlayer->playSound("random.anvil_land", 5.0, 1.0, [$onlinePlayer]);
                }

                $pk = new SetActorLinkPacket();
                $pk->link = new EntityLink($carrier->id, $this->getId(), EntityLink::TYPE_REMOVE, true, true);
                $this->server->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
                return parent::onUpdate($currentTick);
            }

           /* $this->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, $this->getHeadPosition($carrier), 0);
            $this->getDataPropertyManager()->setByte(Entity::DATA_CONTROLLING_RIDER_SEAT_NUMBER, 0);

            $pk = new SetActorLinkPacket();
            $pk->link = new EntityLink($carrier->id, $this->getId(), EntityLink::TYPE_RIDER, true, true);
            $this->server->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
            $this->setPosition($carrier->add($this->getHeadPosition($carrier)));

            $this->yaw = $carrier->yaw;*/
            $this->updateMovement();

            $kit = $fwCarrier->getKit();
            if(!is_null($kit)) {
                if(!$kit->manipulatesFlagMovement()) {
                    $carrier->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 99999, 1, false));
                }
            }

            return parent::onUpdate($currentTick);
        }
        $this->yaw += 5;
        $this->motion->y = -0.3;
        $this->updateMovement();

        if($this->getY() <= 0) {
            $location = $game->getMap()->getRandomFlagLocation();
            $this->teleport($location);
            FlagWarsProvider::createStrike($location->asVector3());
        }

        return parent::onUpdate($currentTick);
    }

    /**
     * @param Player $player
     * @return Vector3
     */
    public function getHeadPosition(Player $player): Vector3 {
        return new Vector3(0, ($player->isSneaking() ? 1.8 : 2), 0);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void {
        $source->setCancelled();

        if(!$source instanceof EntityDamageByEntityEvent) {
            return;
        }
        $player = $source->getDamager();
        if(!$player instanceof Player) {
            return;
        }
        $fwPlayer = FlagWars::getPlayer($player);
        if(!$this->mount($player)) {
            return;
        }
        $team = $fwPlayer->getTeam();
        $team->setHasFlag(true);
        $fwPlayer->setHasFlag(true);

        $carrier = $fwPlayer->getPlayer();
        $this->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, $this->getHeadPosition($carrier), 0);
        $this->getDataPropertyManager()->setByte(Entity::DATA_CONTROLLING_RIDER_SEAT_NUMBER, 0);

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($carrier->id, $this->getId(), EntityLink::TYPE_RIDER, true, true);
        $this->server->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
        $this->setPosition($carrier->add($this->getHeadPosition($carrier)));

        $this->yaw = $carrier->yaw;

        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->sendTip($team->getColor() . "Team " . $team->getName());
            $onlinePlayer->playSound("random.pop", 5.0, 1.0, [$onlinePlayer]);
            $onlinePlayer->sendTitle($team->getColor(). "Team ".$team->getName(), LanguageProvider::getMessageContainer('flag-team-get-subtitle', $onlinePlayer->getName()));
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function mount(Player $player): bool {
        $fwPlayer = FlagWars::getPlayer($player);
        if($fwPlayer->isSpectator()) return false;
        if(!is_null($this->getCarrier())) return false;
        $this->setCarrier($player);
        $this->pickedUp = true;
        $player->toggleSprint($player->isSprinting());
        return true;
    }

    /** @var Player|null */
    private ?Player $carrier = null;

    /**
     * @return Player|null
     */
    public function getCarrier(): ?Player {
        return $this->carrier;
    }

    /**
     * @param Player|null $carrier
     */
    public function setCarrier(?Player $carrier): void {
        $this->carrier = $carrier;

        $team = "White";
        if(!is_null($carrier)) {
            $fwCarrier = FlagWars::getPlayer($carrier);
            $team = $fwCarrier->getTeam()->getName();
        }

        $this->setSkin(new Skin(
            "FlagEntity",
            SkinUtils::fromImage(Settings::SKIN_PATH . "Flag_" . $team . ".png"),
            "",
            "geometry.Mobs.Zombie",
            file_get_contents(Settings::SKIN_PATH . "Flag.geo.json")
        ));
        $this->sendSkin();
    }

    /** @var bool  */
    private bool $pickedUp = false;

    /**
     * @return bool
     */
    public function hasBeenPickedUp(): bool {
        return $this->pickedUp;
    }
}