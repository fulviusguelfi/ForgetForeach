<?php

declare(strict_types=1);
set_include_path(get_include_path() . PATH_SEPARATOR . "core");
spl_autoload_extensions(".php");
spl_autoload_register();

/**
 * Description of ArrayStream
 *
 * @author fulvi
 */
class ArrayStream extends ArrayObject {

    private $iterator, $loopSequence, $iteratorIndex, $collectedArray, $assocArray, $binaryNameMapEntry, $binaryNameListEntry, $bufferedArray;

    public function __construct(
            int $flag = ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS,
            string $iterator = "ArrayIterator",
            int $max_nesting_level = 999999999999999999) {
        $this->setFlags($flag);
        $this->setIteratorClass($iterator);
        ini_set("xdebug.max_nesting_level", (string) $max_nesting_level);
        $this->binaryNameMapEntry = base_convert(unpack('H*', "MapEntry")[1], 16, 2);
        $this->binaryNameListEntry = base_convert(unpack('H*', "ListEntry")[1], 16, 2);
        ;
    }

    public function stream(array $input, array $assocMap = null): ArrayStream {
        $this->bufferedArray = array_chunk($input, 2000, true);
        $this->assocArray = $assocMap;
        return $this;
    }

    public function map(callable $mapper): ArrayStream {
        $this->addHistoryCall("map", $mapper);
        return $this;
    }

    public function filter(callable $filter): ArrayStream {
        $this->addHistoryCall("filter", $filter);
        return $this;
    }

    /**
     * Collect stremed data to a callable function($key, $value){}
     * 
     * @param callable $collector 
     * @return $this: ArrayStream
     */
    public function collect(callable $collector): ArrayStream {
        $this->addHistoryCall("collect", $collector);
        foreach ($this->bufferedArray as $k => $theArr) {
            echo $k;
            parent::__construct($theArr);
            $this->iterator = $this->getIterator();
            $this->callFisrt();
        }
        return $this;
    }

    public function getCollectedArray() {
        return $this->collectedArray;
    }

    private function __map(callable $filter): ArrayStream {
        if ($this->iterator->valid()) {
            $mapped = call_user_func_array($filter, [$this->iterator->key(), $this->iterator->current()]);
            if ($mapped !== null) {
                $this->iterator->offsetSet($this->iterator->key(), $mapped);
                return $this->callNext();
            } else {
                $this->iterator->offsetUnset($this->iterator->key());
                return $this->callFisrt();
            }
        }
        return $this;
    }

    private function __filter(callable $filter): ArrayStream {
        if ($this->iterator->valid()) {
            if ((boolean) call_user_func_array($filter, [$this->iterator->current()])) {
                return $this->callNext();
            } else {
                $this->iterator->offsetUnset($this->iterator->key());
                return $this->callFisrt();
            }
        }
        return $this;
    }

    private function getAssociation($key) {
        if ($this->assocArray !== null && count($this->assocArray) > 0 && array_key_exists($key, $this->assocArray)) {
            return $this->assocArray[$key];
        } else {
            return $key;
        }
    }

    private function setCollection($result) {
        $binaryClassName = base_convert(unpack('H*', get_class($result))[1], 16, 2);
        switch ($binaryClassName) {
            case $this->binaryNameMapEntry:
                $this->collectedArray[$this->getAssociation($result->getKey())] = $result->getVal();
                break;
            case $this->binaryNameListEntry:
                $this->collectedArray[] = $result->getVal();
                break;
            default:
                $this->collectedArray[$this->getAssociation($this->iterator->key())] = $result;
                break;
        }
    }

    private function __collect(callable $collector): ArrayStream {

        if ($this->iterator->valid()) {
            $result = call_user_func_array($collector, [$this->iterator->key(), $this->iterator->current()]);
            $this->setCollection($result);
            return $this->loop();
        }
        return $this;
    }

    private function addHistoryCall(string $calledMethod, $args): void {
        $this->loopSequence[] = ["__" . $calledMethod, $args];
    }

    private function callFisrt(): ArrayStream {
        $this->iteratorIndex = 0;
        return $this->{(string) $this->loopSequence[$this->iteratorIndex][0] }($this->loopSequence[$this->iteratorIndex][1]);
    }

    private function callNext(): ArrayStream {
        $loopSequence = $this->loopSequence[++$this->iteratorIndex];
        return $this->{(string) $loopSequence[0]}($loopSequence[1]);
    }

    private function loop(): ArrayStream {
        $this->iterator->next();
        return $this->callFisrt();
    }

}

//$x = "teste3" == $x;
$starttime = microtime(true);
$array = [];
for ($i = 0; $i <= 525000; $i++) {
    $array[] = "teste" . $i;
}
echo microtime(true) - $starttime;
$starttime = microtime(true);

$test = new ArrayStream();
$arrayCollected = $test->stream($array, [2 => "2zebers", 4 => "4zebers"])
//        ->filter(function ($val) {
//            return  $val >= "teste73"; // $var = "teste3" == $var
//        })
//        ->map(function($key, $val) {
//            return str_replace("teste", "ola", $val);
//        })
//        ->filter(function ($val) {
//            return "ola4" != $val; // $var = "teste3" == $var
//        })
//        ->map(function($key, $val) {
//            return str_replace("ola", "teste", $val);
//        })
        ->collect(Collectors::toList());
//print_r($arrayCollected);
//print_r($arrayCollected->getArrayCopy());
//print_r($arrayCollected->getCollectedArray());
echo microtime(true) - $starttime;
