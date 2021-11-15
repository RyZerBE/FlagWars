<?php


namespace matze\flagwars\listener;


use matze\flagwars\FlagWars;
use matze\flagwars\game\GameManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\CoinProvider;

class PlayerChatListener implements Listener
{
    /** @var array  */
    private array $globalIndex = ["@", "@a", "@g", "@global"];
    /** @var array  */
    private array $ggIndex = ["gg", "nd", "gege", "ggwp"];
    /** @var array  */
    private array $gg = [];

    public function chat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $name = $player->getDisplayName();

        $game = GameManager::getInstance();
        $fwPlayer = FlagWars::getPlayer($player);

        if (($ryzerPlayer = RyZerPlayerProvider::getRyzerPlayer($player->getName())) != null) {
            if ($ryzerPlayer->getMute() === null) {
                $event->setCancelled();
                return;
            }
        }

        if (!$event->isCancelled() && $game->isIngame()) {
            $event->setCancelled();
            if ($player->getGamemode() === 3) {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    if ($player->getGamemode() == 3)
                        $player->sendMessage(TextFormat::GRAY . "[" . TextFormat::RED . "SPEC" . TextFormat::GRAY . "] " . $event->getPlayer()->getNameTag() . TextFormat::GRAY . " | " . TextFormat::YELLOW . $message);
                }
                return;
            }

            if($fwPlayer === null) return;

            if (in_array($message[0], $this->globalIndex)) {
                $message = explode(" ", $message);
                if (isset($message[1])) {
                    unset($message[0]);
                    $message = implode(" ", $message);
                    foreach (Server::getInstance()->getOnlinePlayers() as $player)
                        $player->sendMessage(TextFormat::GRAY . "[" . TextFormat::AQUA . "Global" . TextFormat::GRAY . "] " . $name . TextFormat::GRAY . " | " . TextFormat::WHITE . $message);
                }
            } else {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $oFwPlayer = FlagWars::getPlayer($player);
                    if ($oFwPlayer === null) continue;
                    if ($oFwPlayer->getTeam()->getName() === $fwPlayer->getTeam()->getName()) {
                        $player->sendMessage(TextFormat::GRAY . "[" . TextFormat::GOLD . "Team" . TextFormat::GRAY . "] " . $name . TextFormat::GRAY . " | " . TextFormat::WHITE . $message);
                    }
                }
            }
        }else if($game->isRestarting()) {
            if(in_array($player->getName(), $this->gg) || !in_array($message, $this->ggIndex)) {
                $event->setCancelled();
                foreach (Server::getInstance()->getOnlinePlayers() as $player)
                    $player->sendMessage(TextFormat::GRAY . "[" . TextFormat::RED . "END" . TextFormat::GRAY . "] " . TextFormat::RED . $name . TextFormat::GRAY . " | " . TextFormat::WHITE . $message);
                return;
            }

            $this->gg[] = $player->getName();
            CoinProvider::addCoins($player->getName(), 20);
            $player->sendMessage(FlagWars::PREFIX . LanguageProvider::getMessageContainer('gg-get-coins', $player->getName(), ['#coins' => 20]));
            $player->playSound("random.levelup", 5.0, 1.0, [$player]);
            $event->setCancelled();

            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                if ($event->getPlayer()->hasPermission("gg.vip")) {
                    $player->sendMessage(TextFormat::GRAY . "[" . TextFormat::RED . "END" . TextFormat::GRAY . "] " . TextFormat::RED . $name . TextFormat::GRAY . " | " . TextFormat::WHITE . TextFormat::BOLD . "Oº°‘¨ " . TextFormat::AQUA . $message . TextFormat::WHITE . " ¨‘°ºO");
                } else {
                    $player->sendMessage(TextFormat::GRAY . "[" . TextFormat::RED . "END" . TextFormat::GRAY . "] " . TextFormat::RED . $name . TextFormat::GRAY . " | " . TextFormat::WHITE . $message);
                }
            }
        }
    }
}