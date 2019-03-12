<?php

declare(strict_types=1);
spl_autoload_extensions(".php");
spl_autoload_register();

/**
 *An entry type map (key and value are present)
 *
 * @author fulvi
 */
class MapEntry extends Entry {
/**
 * Construnctor for MapEntry
 * @param mixed $key set the key of this Entry
 * @param mixed $val set the value of this Entry
 */
    public function __construct($key, $val) {
        parent::setKey($key);
        parent::setVal($val);
    }
}
