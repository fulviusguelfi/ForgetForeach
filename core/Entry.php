<?php
declare(strict_types=1);
spl_autoload_extensions(".php");
spl_autoload_register();
/**
 * Abstract class for MapEntry and ListEntry
 *
 * @author fulvi
 */
abstract class Entry {

    private $key, $val;
/**
 * Return the key
 * @return mixed $key
 */
    public function getKey() {
        return $this->key;
    }
    /**
     * Set key
     * @param mixed $key
     */
    public function setKey($key) {
        $this->key = $key;
    }
   /**
    * Return the value
    * @return mixed
    */ 
    public function getVal() {
        return $this->val;
    }
    /**
     * Set value
     * @param mixed $val
     */
    public function setVal($val) {
        $this->val = $val;
    }

}
