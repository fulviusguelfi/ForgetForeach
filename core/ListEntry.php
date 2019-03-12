<?php

declare(strict_types=1);
spl_autoload_extensions(".php");
spl_autoload_register();

/**
 * An entry type list (no key value)
 *
 * @author fulvi
 */
class ListEntry extends Entry {
/**
 * Construnctor for ListEntry
 * @param mixed $val set the value of this Entry
 */
    public function __construct($val) {
        parent::setVal($val);
    }

}
