<?php

namespace matze\flagwars\player;

use BauboLP\Core\Provider\AsyncExecutor;
use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\game\kits\Kit;
use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopMenu;
use matze\flagwars\utils\ItemUtils;
use pocketmine\entity\Effect;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;

class FlagWarsPlayer {

    /** @var Player */
    private $player;

    /** @var array  */
    private $unlockedKits = [];

    /**
     * FlagWarsPlayer constructor.
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
        $this->kit = null;
        $this->shopMenu = new ShopMenu($this);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return bool
     */
    public function isSpectator(): bool {
        return !GameManager::getInstance()->isPlayer($this->getPlayer());
    }

    /**
     * @param string $sound
     * @param int $pitch
     * @param int $volume
     */
    public function playSound(string $sound, int $pitch = 1, int $volume = 1): void {
        $player = $this->getPlayer();
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = $player->x;
        $pk->y = $player->y;
        $pk->z = $player->z;
        $pk->pitch = $pitch;
        $pk->volume = $volume;
        $player->dataPacket($pk);
    }

    /** @var Player|null  */
    private $lastDamager = null;
    /** @var int  */
    private $lastDamageTick = 0;

    /**
     * @param Player|null $player
     */
    public function setLastDamager(?Player $player): void {
        $this->lastDamager = $player;
        if(is_null($player)) {
            $this->lastDamageTick = 0;
            $this->lastDamager = null;//?!?!
            return;
        }
        $this->lastDamageTick = Server::getInstance()->getTick();
    }

    /**
     * @return Player|null
     */
    public function getLastDamager(): ?Player {
        if(($this->lastDamageTick + (12 * 20)) <= Server::getInstance()->getTick()) {
            return null;
        }
        return $this->lastDamager;
    }

    public function reset(): void {
        $player = $this->getPlayer();
        $this->setLastDamager(null);
        $player->removeAllEffects();
        $player->removeTitles();
        $player->sendTitle("§r", "§r", 15, 20, 15);
        $player->doCloseInventory();
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->extinguish();
        $player->setMaxHealth(20);
        $player->setHealth($player->getMaxHealth());
        $player->setFood($player->getMaxFood());
        $player->setXpLevel(0);
        $player->setXpProgress(0);
        $player->setImmobile(false);
        $player->setNameTagVisible(true);
        $player->setNameTagAlwaysVisible(true);
        $player->setInvisible(false);
        $player->setFlying($player->isCreative() && $player->isFlying());
        $player->setAllowFlight($player->isCreative());;
        $player->getInventory()->setHeldItemIndex(0);
    }

    public function getLobbyItems(): void {
        $player = $this->getPlayer();

        $kitSelector = ItemUtils::addItemTag(Item::get(Item::ENDER_CHEST)->setCustomName("§r§aChoose Kit"), "kit_selection", "function");
        $teamSelector = ItemUtils::addItemTag(Item::get(Item::BED)->setCustomName("§r§aChoose Team"), "team_selection", "function");
        $mapSelector = ItemUtils::addItemTag(Item::get(Item::EMPTYMAP)->setCustomName("§r§aChoose Map"), "map_selection", "function");
        $quit = ItemUtils::addItemTag(Item::get(Item::SLIME_BALL)->setCustomName("§r§cQuit Round"), "quit", "function");

        if(GameManager::getInstance()->isRestarting()) {
            $player->getInventory()->setContents([8 => $quit]);
            return;
        }
        $player->getInventory()->setContents([0 => $kitSelector, 3 => $teamSelector, 5 => $mapSelector, 8 => $quit]);
    }

    /** @var Kit|null */
    private $kit = null;

    /**
     * @return Kit|null
     */
    public function getKit(): ?Kit {
        return $this->kit;
    }

    /**
     * @param Kit|null $kit
     */
    public function setKit(?Kit $kit): void {
        $this->kit = $kit;
    }

    /**
     * @return array
     */
    public function getUnlockedKits(): array {
        return $this->unlockedKits;
    }

    /**
     * @param array $unlockedKits
     */
    public function setUnlockedKits(array $unlockedKits): void {
        $this->unlockedKits = $unlockedKits;
    }

    /**
     * @param \matze\flagwars\game\kits\Kit $kit
     * @return bool
     */
    public function boughtKit(Kit $kit)
    {
        return in_array($kit->getName(), $this->unlockedKits);
    }

    /**
     * @param string $kitName
     */
    public function addUnlockedKit(string $kitName)
    {
        $this->unlockedKits[] = $kitName;
    }

    /** @var Team|null */
    private $team = null;

    /**
     * @return Team|null
     */
    public function getTeam(): ?Team {
        return $this->team;
    }

    /**
     * @param Team|null $team
     */
    public function setTeam(?Team $team): void {
        $this->team = $team;
    }

    /** @var string  */
    private $mapVote = "N/A";

    /**
     * @return string
     */
    public function getMapVote(): string {
        return $this->mapVote;
    }

    /**
     * @param string $mapVote
     */
    public function setMapVote(string $mapVote): void {
        $this->mapVote = $mapVote;
    }

    /** @var bool  */
    private $hasFlag = false;

    /**
     * @return bool
     */
    public function hasFlag(): bool {
        return $this->hasFlag;
    }

    /**
     * @param bool $hasFlag
     */
    public function setHasFlag(bool $hasFlag): void {
        $this->hasFlag = $hasFlag;
        if(!$hasFlag) {
            $player = $this->getPlayer();

            $player->removeEffect(Effect::SLOWNESS);
        }
    }
    /** @var \matze\flagwars\shop\ShopMenu */
    private $shopMenu;

    /**
     * @return \matze\flagwars\shop\ShopMenu
     */
    public function getShopMenu(): \matze\flagwars\shop\ShopMenu
    {
        return $this->shopMenu;
    }

    public function load(): void
    {
        $name = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("FlagWars", function (\mysqli $mysqli) use ($name) {
            $res = $mysqli->query("SELECT * FROM kits WHERE playername='$name'");

            if ($res->num_rows > 0)
                return $res->fetch_assoc();
            else
                $mysqli->query("INSERT INTO `kits`(`playername`, `selected_kit`, `kits`) VALUES ('$name', '', '')");

            return null;
        }, function (Server $server, $result) use ($name) {
            if ($result === null) return;
            $player = $server->getPlayer($name);
            if($player === null) return;

            $fwPlayer = FlagWars::getPlayer($player);
            $kits = explode(";", $result["kits"]);
            $selected_kit = $result["selected_kit"];

            if (strlen($selected_kit) > 0)
                $fwPlayer->setKit(GameManager::getInstance()->getKit($selected_kit));

            $fwPlayer->setUnlockedKits($kits);
            $fwPlayer->getPlayer()->playSound("random.levelup", 5.0, 1.0, [$fwPlayer->getPlayer()]);
        });
    }

    public function save()
    {
        $lockedKits = implode(";", $this->getUnlockedKits());
        $kit = $this->getKit()->getName();
        $name = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("FlagWars", function (\mysqli $mysqli) use ($name, $kit, $lockedKits){
             $mysqli->query("UPDATE kits SET selected_kit='$kit',kits='$lockedKits' WHERE playername='$name'");
        });
    }
}