<?php

namespace matze\flagwars\utils;

use matze\marioparty\MarioParty;
use pocketmine\scheduler\ClosureTask;

class TaskExecuter {

    /**
     * @param int $delay
     * @param \Closure $closure
     */
    public static function submitTask(int $delay, \Closure $closure) {
        MarioParty::getLoader()->getScheduler()->scheduleDelayedTask(new ClosureTask($closure), $delay);
    }
}