<?php

declare(strict_types=1);
spl_autoload_extensions(".php");
spl_autoload_register();

/**
 * Not instatiable class
 * provide static methods to collect streamed data
 *
 * @author fulvi
 */
class Collectors {

    protected function __construct() {
        
    }

    /**
     * Return a callable mapping key and value for a MapEntry
     * @return callable
     */
    public static function toMap(): callable {
        return function ($key, $val) {
            return new MapEntry($key, $val);
        };
    }

    /**
     * Return a callable mapping value for a ListEntry
     * @return callable
     */
    public static function toList(): callable {
        return function ($key, $val) {
            return new ListEntry($val);
        };
    }

}
