<?php

namespace matze\flagwars\game;

use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use matze\flagwars\entity\FlagEntity;
use matze\flagwars\entity\ShopEntity;
use matze\flagwars\entity\SpawnerEntity;
use matze\flagwars\FlagWars;
use matze\flagwars\game\kits\Kit;
use matze\flagwars\game\kits\types\AthleteKit;
use matze\flagwars\game\kits\types\BullitKit;
use matze\flagwars\game\kits\types\CutterKit;
use matze\flagwars\game\kits\types\DemolitionistKit;
use matze\flagwars\game\kits\types\SpiderManKit;
use matze\flagwars\game\kits\types\StarterKit;
use matze\flagwars\game\kits\types\VampireKit;
use matze\flagwars\Loader;
use matze\flagwars\utils\FileUtils;
use matze\flagwars\utils\InstantiableTrait;
use matze\flagwars\utils\Settings;
use matze\flagwars\utils\TaskExecuter;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\core\provider\VIPJoinProvider;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\core\util\ItemUtils;
use ryzerbe\core\util\LocationUtils;
use ryzerbe\core\util\Vector3Utils;
use ryzerbe\statssystem\provider\StatsAsyncProvider;
use function array_key_first;
use function array_rand;
use function in_array;
use function krsort;

class GameManager {
    use InstantiableTrait;

    public function __construct() {
        $kits = [
            new StarterKit(),
            new VampireKit(),
            new DemolitionistKit(),
            new SpiderManKit(),
            new BullitKit(),
            new CutterKit(),
            new AthleteKit()
        ];
        foreach ($kits as $kit) {
            $this->registerKit($kit);
        }

        foreach (Settings::$map_pool as $map) {
            $this->registerMap(new Map($map));
        }

        for ($int = 1; $int <= Settings::$total_teams; $int++) {
            $team = Settings::$teams[$int];
            $this->registerTeam(new Team($team["Name"], $team["Color"]));
        }
    }

    /** @var array  */
    private array $teams = [];
    /** @var array  */
    public array $partyTeam = [];

    /**
     * @return Team[]
     */
    public function getTeams(): array {
        return $this->teams;
    }

    /**
     * @param string $team
     * @return Team|null
     */
    public function getTeam(string $team): ?Team {
        return $this->teams[$team] ?? null;
    }

    /**
     * @param Team $team
     */
    public function registerTeam(Team $team): void {
        $this->teams[$team->getName()] = $team;
    }

    /**
     * @return Team|null
     */
    public function findTeam(): ?Team {
        $teams = [];
        foreach ($this->getTeams() as $team) {
            if($team->isFull()) continue;
            $teams[$team->getName()] = count($team->getPlayers());
        }
        if(count($teams) <= 0) {
            return null;
        }
        arsort($teams);
        return $this->getTeam(array_key_first($teams));
    }

    /** @var array  */
    private array $kits = [];

    /**
     * @return Kit[]
     */
    public function getKits(): array {
        return $this->kits;
    }

    /**
     * @param string $kit
     * @return Kit|null
     */
    public function getKit(string $kit): ?Kit {
        return $this->kits[$kit] ?? null;
    }

    /**
     * @param Kit $kit
     */
    public function registerKit(Kit $kit): void {
        $this->kits[$kit->getName()] = $kit;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int {
        return Settings::$players_per_team * Settings::$total_teams;
    }

    /** @var array  */
    private array $players = [];

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player): void {
        $this->players[] = $player->getName();
    }

    /**
     * @param string|Player $player
     */
    public function removePlayer(Player|string $player): void {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        if(!$this->isPlayer($player)) return;
        foreach ($this->getTeams() as $team) $team->removePlayer($player);
        unset($this->players[array_search($player, $this->players)]);
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function isPlayer(Player|string $player): bool {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        return in_array($player, $this->players);
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array {
        $players = [];
        foreach ($this->players as $player) {
            $player = Server::getInstance()->getPlayerExact($player);
            if(!is_null($player)) $players[] = $player;
        }
        return $players;
    }

    /**
     * @return Player[]
     */
    public function getSpectators(): array {
        return array_filter(Server::getInstance()->getOnlinePlayers(),
            function (Player $player): bool {
                return FlagWars::getPlayer($player)->isSpectator();
            });
    }

    /** @var bool  */
    private bool $stats = true;

    /**
     * @param bool $enabled
     */
    public function setStatsEnabled(bool $enabled): void {
        $this->stats = $enabled;
    }

    /**
     * @return bool
     */
    public function statsEnabled(): bool {
        return $this->stats;
    }

    /** @var bool  */
    private bool $forceStart = false;

    /**
     * @param bool $forceStart
     */
    public function setForceStart(bool $forceStart): void {
        $this->forceStart = $forceStart;
    }

    /**
     * @return bool
     */
    public function isForceStart(): bool {
        return $this->forceStart;
    }

    public const STATE_WAITING = 1;
    public const STATE_COUNTDOWN = 2;
    public const STATE_INGAME = 3;
    public const STATE_RESTART = 4;

    /** @var int  */
    private int $state = self::STATE_WAITING;

    /**
     * @return int
     */
    public function getState(): int {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state): void {
        if($this->state === $state) {
            return;
        }
        $this->state = $state;
        switch ($state) {
            case self::STATE_COUNTDOWN: {
                $this->setCountdown(Settings::$waiting_countdown);
                break;
            }
            case self::STATE_INGAME: {
                VIPJoinProvider::disable();
                CloudBridge::getCloudProvider()->addServerToBlackList(CloudProvider::getServer());
                $this->startGame();
                break;
            }
            case self::STATE_RESTART: {
                $this->stopGame();
                break;
            }
        }
    }

    /**
     * @return bool
     */
    public function isIngame(): bool{
        return $this->getState() === self::STATE_INGAME;
    }

    /**
     * @return bool
     */
    public function isRestarting(): bool {
        return $this->getState() === self::STATE_RESTART;
    }

    /**
     * @return bool
     */
    public function isWaiting(): bool {
        return $this->getState() === self::STATE_WAITING;
    }

    /**
     * @return bool
     */
    public function isCountdown(): bool {
        return $this->getState() === self::STATE_COUNTDOWN;
    }

    /** @var int  */
    private int $countdown = 60;

    /**
     * @param int $countdown
     */
    public function setCountdown(int $countdown): void {
        $this->countdown = $countdown;
    }

    /**
     * @return int
     */
    public function getCountdown(): int {
        return $this->countdown;
    }

    public function tickCountdown(): void {
        if($this->getCountdown() <= 0) {
            return;
        }
        $this->countdown--;
    }

    /** @var array  */
    private array $map_pool = [];

    /**
     * @return array
     */
    public function getMapPool(): array {
        return $this->map_pool;
    }

    /**
     * @param Map $map
     */
    public function registerMap(Map $map): void {
        $this->map_pool[$map->getName()] = $map;
    }

    /**
     * @param string $map
     * @return Map|null
     */
    public function getMapByName(string $map): ?Map {
        return $this->map_pool[$map] ?? null;
    }

    public function loadMap(): void {
        //I hate those things....
        $map = $this->getTopMap();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function () use ($map): void {
            if(is_dir("worlds/" . $map)) {
                FileUtils::delete("worlds/" . $map);
            }
            @mkdir("worlds/" . $map);
            FileUtils::copy(Settings::MAPS_PATH . $map, "worlds/" . $map);
        }, function (Server $server, $result) use ($map): void {
            $server->loadLevel($map);
            $map = $server->getLevelByName($map);
            $game = GameManager::getInstance();
            $game->setMap($game->getMapByName($map->getFolderName()));

            $map->setTime(6000);
            $map->stopTime();

            foreach ($server->getOnlinePlayers() as $player) {
                $player->sendTitle(TextFormat::GOLD.$game->getMap()->getName());
            }
        });
    }

    public function getTopMap(): string{
        $mapVotes = [];
        $maps = [];
        foreach ($this->getMapPool() as $mapName => $map) {
            $mapVotes[$mapName] = 0;
            $maps[] = $mapName;
        }
        foreach ($this->getPlayers() as $player) {
            $fwPlayer = FlagWars::getPlayer($player);
            if(!in_array($fwPlayer->getMapVote(), $maps)) continue;
            $mapVotes[$fwPlayer->getMapVote()]++;
        }
        krsort($mapVotes);
        $maps = [];
        $index = 1;
        foreach ($mapVotes as $map => $votes) {
            if(!isset($maps[$index])) $maps[$index] = [];
            $maps[$index][] = $map;
        }
        $topMaps = $maps[1];
        return $topMaps[array_rand($topMaps)];
    }

    /** @var Map|null */
    private ?Map $map = null;

    /**
     * @return Map|null
     */
    public function getMap(): ?Map {
        return $this->map;
    }

    /**
     * @param Map|null $map
     */
    public function setMap(?Map $map): void {
        $this->map = $map;
    }

    public function startGame(): void {
        $map = $this->getMap();
        $this->setCountdown(60);
        foreach ($this->getPlayers() as $player) {
            $fwPlayer = FlagWars::getPlayer($player);

            if(is_null($fwPlayer->getTeam())) {
                $fwPlayer->setTeam($this->findTeam());
            }
            if(is_null($fwPlayer->getTeam())) {
                $player->sendMessage("§c§oSomething went wrong. Team cannot be null.");
                Server::getInstance()->dispatchCommand($player, "hub");
                continue;
            }
            $team = $fwPlayer->getTeam();
            $team->addPlayer($player);
            $fwPlayer->reset();
            $player->setGamemode(0);
            $player->teleport($team->getSpawnLocation());
            $player->setImmobile();
            $player->setNameTag($team->getColor().$player->getName());
            $player->setDisplayName($team->getColor().$player->getName());

            StatsAsyncProvider::appendStatistic($player->getName(), Loader::STATS_CATEGORY, "rounds", 1);

            $kit = $fwPlayer->getKit();
            if(!is_null($kit)) foreach ($kit->getItems($player) as $item) $player->getInventory()->addItem(ItemUtils::addItemTag($item, "kit_item", "kit_item"));
        }
        foreach ($this->getSpectators() as $spectator) {
            $spectator->teleport($map->getSpectatorLocation());
        }

        TaskExecuter::submitTask(40, function (int $tick): void {
            foreach (GameManager::getInstance()->getPlayers() as $player) {
                $player->setImmobile(false);
            }
        });

        foreach ($map->getSpawner() as $location => $data) {
            $location = LocationUtils::fromString($location);
            $type = $data["Type"];

            $nbt = Entity::createBaseNBT($location);
            $nbt->setString("Type", $type);
            $spawner = new SpawnerEntity($location->getLevel(), $nbt);
            $spawner->spawnToAll();
        }

        foreach ($map->getShopLocations() as $location) {
            $nbt = Entity::createBaseNBT($location);
            $shop = new ShopEntity($location->getLevel(), $nbt);
            $shop->spawnToAll();
        }

      /*  $replay = new Replay($map->getSpectatorLocation()->getLevel());
        $replay->setSpawn($map->getSpectatorLocation()->asVector3());
        $replay->startRecording();
        ReplayManager::getInstance()->addReplay($replay);*/
    }

    public function stopGame(): void {
        $this->setCountdown(15);

        $winner = null;
        $winners = array_filter($this->getTeams(), function (Team $team): bool {return $team->getFlagsSaved() >= Settings::$flag_to_win;});
        foreach ($winners as $team) $winner = $team;

        if($winner === null)
            $winner = $this->getTeams()[array_key_first($this->getTeams())] ?? null;

       /* $replay = ReplayManager::getInstance()->getReplayByLevel($this->getMap()->getSpectatorLocation()->getLevel());
        $replay->stopRecording();*/

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $fwPlayer = FlagWars::getPlayer($player);
            if($fwPlayer === null) continue;
            $player->teleport(Settings::$waiting_lobby_location);
            $fwPlayer->reset();
            $fwPlayer->getLobbyItems();

            if($winner !== null) {
                $player->sendTitle($winner->getColor()."Team ".$winner->getName(), "HGW <3");
            }

            $player->playSound("firework.launch", 5.0, 1.0, [$player]);
            if($fwPlayer->isSpectator()) continue;
            if($fwPlayer->getTeam()->getName() === $winner?->getName()){
                CoinProvider::addCoins($player->getName(), rand(100, 300));
                RyZerPlayerProvider::getRyzerPlayer($player->getName())->getNetworkLevel()->addProgress(50);
                StatsAsyncProvider::appendStatistic($player->getName(), Loader::STATS_CATEGORY, "wins", 1);
            }else{
                StatsAsyncProvider::appendStatistic($player->getName(), Loader::STATS_CATEGORY, "loses", 1);
                CoinProvider::addCoins($player->getName(), rand(50, 100));
            }
        }
    }

    /** @var array  */
    private array $blocks = [];

    /**
     * @param Block $block
     */
    public function addBlock(Block $block): void {
        $this->blocks[] = Vector3Utils::toString($block->floor());
    }

    /**
     * @param Block $block
     * @return bool
     */
    public function isBlock(Block $block): bool {
        return in_array(Vector3Utils::toString($block->floor()), $this->blocks);
    }

    /**
     * @param Block $block
     */
    public function removeBlock(Block $block): void {
        if(!$this->isBlock($block)) return;
        unset($this->blocks[array_search(Vector3Utils::toString($block->floor()), $this->blocks)]);
    }

    /** @var bool  */
    private bool $flag = false;

    /**
     * @return bool
     */
    public function isFlag(): bool {
        return $this->flag;
    }

    /**
     * @param bool $flag
     */
    public function setFlag(bool $flag): void {
        $this->flag = $flag;
        if(!$flag) $this->setCountdown(45);
    }

    /**
     * @return FlagEntity|null
     */
    public function getFlag(): ?FlagEntity {
        if(!$this->isFlag()) return null;
        foreach (Server::getInstance()->getLevelByName($this->getMap()->getName())->getEntities() as $entity) {
            if($entity instanceof FlagEntity) return $entity;
        }
        return null;
    }
}