<?php

namespace matze\flagwars\utils;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;

class Scoreboard {

    /**
     * @param Player $player
     * @param int $score
     * @param string $line
     */
    public static function setLine(Player $player, int $score, string $line = ""): void {
        if(empty($line)) {
            for ($n = 0; $n <= 5; $n++) {$line .= "§" . mt_rand(0, 9);}
        }
        $entrie = new ScorePacketEntry();
        $entrie->objectiveName = "scoreboard";
        $entrie->type = 3;
        $entrie->customName = " " . $line . " ";
        $entrie->score = $score;
        $entrie->scoreboardId = $score;
        $pk = new SetScorePacket();
        $pk->type = 1;
        $pk->entries[] = $entrie;
        $pk2 = new SetScorePacket();
        $pk2->entries[] = $entrie;
        $pk2->type = 0;
        $player->sendDataPacket($pk);
        $player->sendDataPacket($pk2);
    }

    /**
     * @param Player $player
     * @param string $title
     */
    public static function sendScoreboard(Player $player, string $title): void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = "sidebar";
        $pk->objectiveName = "scoreboard";
        $pk->displayName = $title;
        $pk->criteriaName = "dummy";
        $pk->sortOrder = 0;
        $player->sendDataPacket($pk);
    }

    /**
     * @param Player $player
     */
    public static function removeScoreboard(Player $player): void {
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = "scoreboard";
        $player->sendDataPacket($pk);
    }
}