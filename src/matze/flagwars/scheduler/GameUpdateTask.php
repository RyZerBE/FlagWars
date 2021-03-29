<?php

namespace matze\flagwars\scheduler;

use matze\flagwars\game\GameManager;
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
        $missingPlayers = 1;
        switch ($game->getState()) {
            case $game::STATE_WAITING: {
                Server::getInstance()->broadcastTip("§r§aWarten auf §6" . $missingPlayers . " Spieler§a" . str_repeat(".", $this->points));
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
                Server::getInstance()->broadcastTip("§r§aRunde startet in §6" . $game->getCountdown() . " Sekunde" . ($game->getCountdown() === 1 ? "" : "n") . "§a" . str_repeat(".", $this->points));
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
                Server::getInstance()->broadcastTip("§r§aServer startet in §6" . $game->getCountdown() . " Sekunde" . ($game->getCountdown() === 1 ? "" : "n") . "§a neu" . str_repeat(".", $this->points));
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