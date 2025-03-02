<?php

namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;

abstract class ListObject implements \ArrayAccess, \Iterator
{
    protected $containter = [];
    private const
        METHOD_ALIASES = [
        "gecet" => ["get", "onlyindex", "cut", "count","object"],
        "compare" => ["equal", "notequal", "smallerthan", "lessthan", "greaterthan", "regex"]
    ];
    protected $class;
    protected $pointer = 0;

    private  $calls = 0;
    /**
     * @var finInfo $findinfo
     */
    private $findinfo;
    public const
        SEARCH_EQUAL = 1,
        SEARCH_NOT_EQUAL = 2,
        SEARCH_SMALLER_THAN = 3,
        SEARCH_GREATER_THAN = 4,
        SEARCH_REGEX = 4;

    private $findcount = 0;

    public function reset()
    {
        $this->containter = [];
    }

    public function search($search, $mod = self::SEARCH_EQUAL, $only_index = false)
    {
        $return = [];

        foreach ($this->containter as $index => $item) {
            if (is_array($search)) {
                $key = array_keys($search);
                $method = $key[0];
                //$m = (method_exists($this,$item->$method)?$item->$method:"");
                if (($item->$method == $search[$key[0]] && $mod == self::SEARCH_EQUAL)
                    || ($item->$method != $search[$key[0]] && $mod == self::SEARCH_NOT_EQUAL)
                    || ($item->$method < $search[$key[0]] && $mod == self::SEARCH_SMALLER_THAN)
                    || ($mod == self::SEARCH_REGEX && !is_null($item->{$method}) && preg_match(@$search[$method], $item->{$method}))
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
                        || ($mod == self::SEARCH_REGEX && preg_match($search, $value))
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
        //print_r($return);
        $this->findcount = count($return);
        return $return;
    }

    private function set($param)
    {
        if (!is_array($param)) {
            throw new \Exception(Messages::LISTO_PARAMETER_IS_NOT_ARRAY);
        }
        $ret = $this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare, true);
        $keys = array_keys($param);
        foreach ($ret as $index => $item) {
            $this->containter[$item]->{$keys[0]} = $param[$keys[0]];
        }
    }

    public function find(string $name)
    {
        $this->findcount = 0;
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
            case "lessthan":
                $type = self::SEARCH_SMALLER_THAN;
                break;
            default:
                $type = self::SEARCH_GREATER_THAN;

        }
        $this->findinfo->compare = $type;
        $this->findinfo->toCompare = $value;
        return new compare2RequestCallBack($this);


    }

    /**
     * Alias funkció
     * @param $operation
     * @return array|void|int
     */
    private function gecet($operation)
    {
        //print_r(func_get_args());
        if ($operation == "cut") {
            $ret = $this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare, true);
            foreach ($ret as $index) {
                unset($this->containter[$index]);
            }
            $this->reindex();
        } elseif ($operation == "count") {
            return count($this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare, true));
        } elseif ($operation == "object") {

            $ret = $this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare);
            $class = get_called_class();
            $return = new $class();
            foreach ($ret as $item) {
                $return->push($item);
            }
            return $return;
        } else {
            return $this->search([$this->findinfo->name => $this->findinfo->toCompare], $this->findinfo->compare, ($operation == "onlyindex" ? true : false));
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
        $this->containter = array_values($this->containter);
    }

    public function count(): int
    {
        if ($this->calls > 0) {
            return $this->gecet("count");
        }
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

    public function offsetSet($offset, $value): void
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
        $this->calls++;
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

    /***
     * @var array $container
     */
    protected  $container;
    /**
     * @var array $keys
     */
    protected  $keys;

    /**
     * @var int $pointer
     */
    private $pointer = 0;

    public function __construct($array = null)
    {
        if (is_object($array)) {
            foreach ($array->container as $key => $value) {
                $this->container[$key] = $value;

            }
        } elseif (is_array($array)) {
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
 * @method compare2RequestCallBack lessthan($value_with_compare)
 * @method compare2RequestCallBack greaterthan($value_with_compare)
 */
final class findRequestCallBack extends CallbackBase
{
}

/**
 * @method array    get()
 * @method object    object()
 * @method void    set(array $set)
 * @method array    onlyindex()
 * @method void     cut()
 * @method int     count()
 * @method findRequestCallBack or ($name)
 * @method findRequestCallBack and ($name)
 */
final class compare2RequestCallBack extends CallbackBase
{
}


final class finInfo
{
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var string $toCompare
     */
    public $toCompare;
    /**
     * @var int $compare
     */
    public $compare;
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