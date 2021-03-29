<?php

namespace matze\flagwars\utils;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class AsyncExecuter {

    /**
     * @param callable $function
     * @param callable|null $completionFunction
     * @phpstan-param callable(Server $server, mixed $result): void
     */
    public static function submitAsyncTask(callable $function, ?callable $completionFunction = null): void {
        Server::getInstance()->getAsyncPool()->submitTask(
            new class($function, $completionFunction) extends AsyncTask {

                /** @var callable */
                private $function;
                /** @var callable|null */
                private $completionFunction;

                /**
                 *  constructor.
                 * @param callable $function
                 * @param callable|null $completionFunction
                 */
                public function __construct(callable $function, ?callable $completionFunction) {
                    $this->function = $function;
                    $this->completionFunction = $completionFunction;
                }

                public function onRun(): void {
                    $this->setResult(($this->function)());
                }

                /**
                 * @param Server $server
                 */
                public function onCompletion(Server $server): void {
                    if(is_null($this->completionFunction)) {
                        return;
                    }
                    ($this->completionFunction)($server, $this->getResult());
                }
            }
        );
    }
}