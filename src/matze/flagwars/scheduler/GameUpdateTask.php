<?php

namespace matze\flagwars\scheduler;

use matze\flagwars\game\GameManager;
use matze\flagwars\utils\Settings;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class GameUpdateTask extends Task {

    /** @var int  */
    private $points = 0;

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick): void {
        $this->points++;
        if($this->points > 3) {
            $this->points = 0;
        }
        $game = GameManager::getInstance();
        $missingPlayers = Settings::$players_for_start - count($game->getPlayers());
        switch ($game->getState()) {
            case $game::STATE_WAITING: {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    if($player->isCreative()) continue;//For setup
                    $player->sendTip("Missing players: " . $missingPlayers . str_repeat(".", $this->points));//todo: popup
                }
                if($missingPlayers > 0 && !$game->isForceStart()) {
                    break;
                }
                $game->setState($game::STATE_COUNTDOWN);
            }
            case $game::STATE_COUNTDOWN: {
                if(!$game->isForceStart()) {
                    if($missingPlayers > 0) {
                        $game->setState($game::STATE_WAITING);
                        break;
                    }
                } else {
                    if(count($game->getPlayers()) <= 1) {
                        $game->setState($game::STATE_WAITING);
                        $game->setForceStart(false);
                        break;
                    }
                }
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $player->sendTip("Time until start: " . $game->getCountdown() . str_repeat(".", $this->points));//todo: popup
                }
                $game->tickCountdown();
                if($game->getCountdown() <= 0) {
                    $game->setState($game::STATE_INGAME);
                }
                break;
            }
            case $game::STATE_INGAME: {
                //todo
                break;
            }
            case $game::STATE_RESTART: {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $player->sendTip("Server will be stopped: " . $game->getCountdown() . str_repeat(".", $this->points));//todo: popup
                }
                $game->tickCountdown();
                if($game->getCountdown() === 3) {
                    foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                        Server::getInstance()->dispatchCommand($player, "hub");
                    }
                }
                if($game->getCountdown() === 0) {
                    $game->setState(-1);
                    Server::getInstance()->shutdown();
                }
                break;
            }
        }
    }
}