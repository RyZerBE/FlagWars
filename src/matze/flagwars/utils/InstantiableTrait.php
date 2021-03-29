<?php

namespace matze\flagwars\utils;

trait InstantiableTrait {

    /** @var static $instance|null  */
    private static $instance = null;

    /**
     * @return static
     */
    public static function getInstance(): self {
        if(is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}