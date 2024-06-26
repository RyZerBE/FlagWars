<?php

namespace matze\flagwars\player;

use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use matze\flagwars\game\kits\Kit;
use matze\flagwars\game\Team;
use matze\flagwars\shop\ShopMenu;
use mysqli;
use pocketmine\block\BlockIds;
use pocketmine\entity\Effect;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\provider\PartyProvider;
use ryzerbe\core\RyZerBE;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\core\util\ItemUtils;
use ryzerbe\core\util\Settings;

class FlagWarsPlayer {

    /** @var Player */
    private Player $player;

    /** @var array  */
    private array $unlockedKits = [];

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
    private ?Player $lastDamager = null;
    /** @var int  */
    private int $lastDamageTick = 0;

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
        $player->setExhaustion(0.0);
        $player->setSaturation(20.0);
        $player->setXpLevel(0);
        $player->setXpProgress(0);
        $player->setImmobile(false);
        $player->setNameTagVisible(true);
        $player->setNameTagAlwaysVisible(true);
        $player->setInvisible(false);
        $player->setFlying($player->isCreative() && $player->isFlying());
        $player->setAllowFlight($player->isCreative());
        $player->getInventory()->setHeldItemIndex(0);
    }

    public function getLobbyItems(): void {
        $player = $this->getPlayer();

        $kitSelector = ItemUtils::addItemTag(Item::get(BlockIds::ENDER_CHEST)->setCustomName("§r§aChoose Kit"), "kit_selection", "function");
        $teamSelector = ItemUtils::addItemTag(Item::get(ItemIds::BED)->setCustomName("§r§aChoose Team"), "team_selection", "function");
        $mapSelector = ItemUtils::addItemTag(Item::get(ItemIds::EMPTYMAP)->setCustomName("§r§aChoose Map"), "map_selection", "function");
        $info = ItemUtils::addItemTag(Item::get(ItemIds::BOOK)->setCustomName("§r§aInfo"), "info", "function");
        $quit = ItemUtils::addItemTag(Item::get(ItemIds::SLIME_BALL)->setCustomName("§r§cQuit Round"), "quit", "function");

        if(GameManager::getInstance()->isRestarting()) {
            $player->getInventory()->setContents([8 => $quit]);
            return;
        }
        $player->getInventory()->setContents([0 => $kitSelector, 2 => $teamSelector, 4 => $info, 6 => $mapSelector, 8 => $quit]);
    }

    /** @var Kit|null */
    private ?Kit $kit;

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
     * @param Kit $kit
     * @return bool
     */
    public function boughtKit(Kit $kit): bool{
        return in_array($kit->getName(), $this->unlockedKits) || $this->getPlayer()->hasPermission("kits.free");
    }

    /**
     * @param string $kitName
     */
    public function addUnlockedKit(string $kitName)
    {
        $this->unlockedKits[] = $kitName;
    }

    /** @var Team|null */
    private ?Team $team = null;

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
    private string $mapVote = "N/A";

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
    private bool $hasFlag = false;

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
    /** @var ShopMenu */
    private ShopMenu $shopMenu;

    /**
     * @return ShopMenu
     */
    public function getShopMenu(): ShopMenu
    {
        return $this->shopMenu;
    }

    public function load(): void
    {
        $name = $this->getPlayer()->getName();
        $mysqlData = Settings::$mysqlLoginData;
        AsyncExecutor::submitMySQLAsyncTask("FlagWars", function (mysqli $mysqli) use ($name, $mysqlData) {
            $res = $mysqli->query("SELECT * FROM kits WHERE playername='$name'");

            $pData = [];
            if ($res->num_rows > 0)
                $pData["kits"] = $res->fetch_assoc();
            else
                $mysqli->query("INSERT INTO `kits`(`playername`, `selected_kit`, `kits`) VALUES ('$name', '', '')");


            $coreDB = new mysqli($mysqlData["host"], $mysqlData["username"], $mysqlData["password"], "RyZerCore");
            $party = PartyProvider::getPartyByPlayer($coreDB, $name);
            if($party !== null) {
                $members = PartyProvider::getPartyMembers($coreDB, $party);
                if($members > 1){
                    $pData["party"]["owner"] = $party;
                    $pData["party"]["members"] = $members;
                }
            }

            $coreDB->close();

            return $pData;
        }, function (Server $server, $result) use ($name) {
            if ($result === null) return;
            $player = $server->getPlayer($name);
            if($player === null) return;

            $fwPlayer = FlagWars::getPlayer($player);
            $kits = explode(";", $result["kits"]["kits"] ?? "");
            $selected_kit = $result["kits"]["selected_kit"] ?? "";

            if (strlen($selected_kit) > 0)
                $fwPlayer->setKit(GameManager::getInstance()->getKit($selected_kit));

            $fwPlayer->setUnlockedKits($kits);
            if(isset($loadedData["party"])) {
                $members = $loadedData["party"]["members"];
                $teamName = GameManager::getInstance()->partyTeam[$loadedData["party"]["owner"]] ?? null;
                if($teamName !== null) {
                    $team = GameManager::getInstance()->getTeam($teamName);
                    $team?->join($fwPlayer);
                    $fwPlayer->getPlayer()->sendMessage(RyZerBE::PREFIX.LanguageProvider::getMessageContainer("party-join-team", $fwPlayer->getPlayer(), ["#team" => $team->getColor().$team->getName()]));
                    return;
                }
                $found = false;
                foreach(GameManager::getInstance()->getTeams() as $team){
                    if($team->isFull()) continue;
                    if((\matze\flagwars\utils\Settings::$players_per_team - count($team->getPlayers())) < count($members)) continue;

                    GameManager::getInstance()->partyTeam[$loadedData["party"]["owner"]] = $team->getName();
                    $team->join($fwPlayer);
                    $fwPlayer->getPlayer()->sendMessage(RyZerBE::PREFIX.LanguageProvider::getMessageContainer("party-join-team", $fwPlayer->getPlayer(), ["#team" => $team->getColor().$team->getName()]));
                    $found = true;
                    break;
                }

                if(!$found) {
                    $fwPlayer->getPlayer()->sendMessage(RyZerBE::PREFIX.LanguageProvider::getMessageContainer("no-free-team-party", $fwPlayer->getPlayer()));
                }
            }
            $fwPlayer->getPlayer()->playSound("random.levelup", 5.0, 1.0, [$fwPlayer->getPlayer()]);
        });
    }

    public function save()
    {
        $lockedKits = implode(";", $this->getUnlockedKits());
        if ($this->getKit() != null)
            $kit = $this->getKit()->getName();
        else
            $kit = "";

        $name = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("FlagWars", function (mysqli $mysqli) use ($name, $kit, $lockedKits) {
            $mysqli->query("UPDATE kits SET selected_kit='$kit',kits='$lockedKits' WHERE playername='$name'");
        });
    }
}