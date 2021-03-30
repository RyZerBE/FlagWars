<?php

namespace matze\flagwars\utils;

use matze\flagwars\FlagWars;
use pocketmine\scheduler\ClosureTask;

class TaskExecuter {

    /**
     * @param int $delay
     * @param \Closure $closure
     */
    public static function submitTask(int $delay, \Closure $closure) {
        FlagWars::getLoader()->getScheduler()->scheduleDelayedTask(new ClosureTask($closure), $delay);
    }
}