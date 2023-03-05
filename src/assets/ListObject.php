<?php

namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;

abstract class ListObject implements \ArrayAccess, \Iterator
{
    protected $containter = [];

    private const
        METHOD_ALIASES = [
        "gecet" => ["get", "onlyindex", "cut"],
        "compare" => ["equal", "notequal", "smallerthan", "greaterthan","regex"]
    ];
    protected $class;
    protected $pointer = 0;

    private finInfo $findinfo;
    public const
        SEARCH_EQUAL = 1,
        SEARCH_NOT_EQUAL = 2,
        SEARCH_SMALLER_THAN = 3,
        SEARCH_GREATER_THAN = 4,
        SEARCH_REGEX = 4;

    private array $findIndexes = [];
    public function reset(){
        $this->containter = [];
    }
    public function search($search, $mod = self::SEARCH_EQUAL, $only_index = false)
    {
        $return = [];

        foreach ($this->containter as $index => $item) {
            if (is_array($search)) {
                $key = array_keys($search);
                $method = $key[0];
                if (($item->$method == $search[$key[0]] && $mod == self::SEARCH_EQUAL)
                    || ($item->$method != $search[$key[0]] && $mod == self::SEARCH_NOT_EQUAL)
                    || ($item->$method < $search[$key[0]] && $mod == self::SEARCH_SMALLER_THAN)
                    || ($mod == self::SEARCH_REGEX && !is_null($item->{$method}) && preg_match(@$search[$method],$item->{$method}))
                ) {
                    if ($only_index) {
                        $return[] = $index;
                    } else {
                        $return[] = $item;
                    }
                }
            } else {
                foreach ($item as $key => $value) {
                    if (($value == $search && $mod == self::SEARCH_EQUAL)
                        || ($value != $search && $mod == self::SEARCH_NOT_EQUAL)
                        || ($value < $search && $mod == self::SEARCH_SMALLER_THAN)
                        || ($mod == self::SEARCH_REGEX && preg_match($search,$value))
                    ) {
                        if ($only_index) {
                            $return[] = $index;
                        } else {
                            $return[] = $item;
                        }
                        break;
                    }
                }
            }
        }
        // print_r($return);
        $this->findIndexes = $return;
        return $return;
    }

    private function set($param){
        if (!is_array($param)){
            throw new \Exception(Messages::LISTO_PARAMETER_IS_NOT_ARRAY);
        }
        $ret = $this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare, true);
        $keys = array_keys($param);
        foreach ($ret AS $index => $item){
            $this->containter[$item]->{$keys[0]} = $param[$keys[0]];
        }
    }
    public function find(string $name)
    {
        $this->findIndexes = [];
        $this->findinfo = new finInfo();
        $this->findinfo->name = $name;
        return new findRequestCallBack($this);
    }

    public function pop(Route $route): void
    {
        $this->containter = array_merge([$route], $this->containter);
    }

    private function compare($type, $value): compare2RequestCallBack
    {
        switch ($type) {
            case "equal":
                $type = self::SEARCH_EQUAL;
                break;
            case "notequal":
                $type = self::SEARCH_NOT_EQUAL;
                break;
            case "smallerthan":
                $type = self::SEARCH_SMALLER_THAN;
                break;
            default:
                $type = self::SEARCH_GREATER_THAN;

        }
        $this->findinfo->compare = $type;
        $this->findinfo->toCompare = $value;
        return new compare2RequestCallBack($this);


    }

    private function gecet($operation)
    {
        if ($operation == "cut") {
            $ret = $this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare, true);
            foreach ($ret as $index) {
                unset($this->containter[$index]);
            }
            $this->reindex();
        }
        else{
            return $this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare, ($operation=="onlyindex"?true:false));
        }
    }

    /**
     * @param int|array[] $index
     * @return void
     */
    public function delete($index): void
    {
        if (is_array($index)) {
            foreach ($index as $value) {
                unset($this->containter[$value]);
            }
        } else {
            unset($this->containter[$index]);
        }
        $this->reindex();
    }

    private function reindex()
    {
        /*
        $t = [];
        foreach ($this->containter AS $item){
            $t[] = $item;
        }
        $this->containter = $t;
        */
        $this->containter = array_values($this->containter);
    }

    public function count(): int
    {
        return count($this->containter);
    }
/*
    public function match(array|string $search)
    {
        $return = [];
        foreach ($this->containter as $item) {
            if (is_array($search)) {
                $key = array_keys($search);
                $method = $key[0];
                if (preg_match("/" . $search[$key[0]] . "/", $item->$method)) {
                    $return[] = $item;
                    break;
                }
            } else {
                foreach ($item as $key => $value) {
                    if (!is_array($value)) {
                        if (preg_match("/" . $search . "/", $value)) {
                            $return[] = $item;
                            break;
                        }
                    }
                }
            }
        }
        return $return;
    }
*/
    public function push($array): void
    {
        $classname = $this->class;
        $this->containter[] = new $classname($array);
    }

    public function offsetExists($offset): bool
    {
        if (isset($this->containter[$offset])) {
            return true;
        }
        return false;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->containter[$offset];
    }

    public function offsetSet($offset,$value): void
    {
        if (is_array($value)) {
            $this->containter[$offset] = new $this->class($value);
        } else {
            $this->containter[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->containter[$offset]);
        $this->reindex();
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->containter[$this->pointer];
    }

    public function next(): void
    {
        $this->pointer++;
    }

    public function key(): mixed
    {
        return $this->pointer;
    }

    public function valid(): bool
    {
        return $this->pointer < count($this->containter);
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function __call(string $name, array $arguments)
    {
        $name = $this->method_alias($name, $arguments);

        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        } else {

        }
    }

    private function method_alias($name, &$params)
    {
        foreach (self::METHOD_ALIASES as $key => $alias) {
            if (in_array($name, $alias)) {
                $params = array_merge([$name], $params);
                return $key;
            }
        }
        return $name;
    }


}

abstract class ListObjectItem implements \ArrayAccess
{

    protected array $container;
    protected array $keys;

    private int $pointer = 0;

    public function __construct($array = null)
    {
        if (is_object($array)) {
            foreach ($array->container as $key => $value) {
                $this->container[$key] = $value;

            }
        } elseif(is_array($array)) {
            foreach ($array as $key => $value) {
                $this->container[$key] = $value;

            }
        }
    }


    public function offsetExists($offset): bool
    {
        if (isset($this->container[$offset])) {
            return true;
        }
        return false;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (!isset($this->container[$offset])) {
            $this->keys[] = $offset;
        }
        $this->container[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        foreach ($this->keys as $index => $key) {
            if ($key == $offset) {
                unset($this->keys[$index]);
            }
        }
        unset($this->container[$offset]);
    }

    public function __set($name, $value): void
    {
        if (!isset($this->container[$name])) {
            $this->keys[] = $name;
        }
        $this->container[$name] = $value;
    }

    public function __get(string $name)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }
        return null;
    }
}

/**
 * @method compare2RequestCallBack regex($value_with_compare)
 * @method compare2RequestCallBack equal($value_with_compare)
 * @method compare2RequestCallBack notequal($value_with_compare)
 * @method compare2RequestCallBack smallerthan($value_with_compare)
 * @method compare2RequestCallBack greaterthan($value_with_compare)
 */
final class findRequestCallBack extends CallbackBase{}

/**
 * @method array    get()
 * @method void    set(array $set)
 * @method array    onlyindex()
 * @method void     cut()
 * @method findRequestCallBack or($name)
 * @method findRequestCallBack and($name)
 */
final class compare2RequestCallBack extends CallbackBase{}


final class finInfo
{
    public string $name;
    public string $toCompare;
    public int $compare;
}
/*
abstract class toCompareBase
{
    protected $arguments;

    public function __construct($parentclass)
    {
        $this->arguments = $parentclass;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->arguments->{$name}(...$arguments);
    }
}*/