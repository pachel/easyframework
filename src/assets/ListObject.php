<?php

namespace Pachel\EasyFrameWork;

abstract class ListObject implements \ArrayAccess, \Iterator
{
    protected $containter;

    protected $class;
    protected $pointer = 0;

    public function search(array|string $search)
    {
        $return = [];
        foreach ($this->containter as $item) {
            if (is_array($search)) {
                $key = array_keys($search);
                $method = $key[0];

                if ($item->$method == $search[$key[0]]) {
                    $return[] = $item;
                }
            } else {
                foreach ($item as $key => $value) {
                    if ($value == $search) {
                        $return[] = $item;
                        break;
                    }
                }
            }
        }
        return $return;
    }

    public function match(array|string $search)
    {
        $return = [];
        foreach ($this->containter as $item) {
            if (is_array($search)) {
                $key = array_keys($search);
                $method = $key[0];
                if (preg_match("/".$search[$key[0]]."/",$item->$method)) {
                    $return[] = $item;
                    break;
                }
            } else {
                foreach ($item as $key => $value) {
                    if(!is_array($value)) {
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

    public function push($array): void
    {
        $classname = $this->class;
        $this->containter[] = new $classname($array);
    }

    public function offsetExists(mixed $offset): bool
    {
        if (isset($this->containter[$offset])) {
            return true;
        }
        return false;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset)
    {
        return $this->containter[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_array($value)) {
            $this->containter[$offset] = new $this->class($value);
        } else {
            $this->containter[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->containter[$offset]);
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

}

abstract class ListObjectItem implements \ArrayAccess
{
    public function __construct(array $array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        if (property_exists($this, $offset)) {
            return true;
        }
        return false;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->$offset);
    }

    public function __set(string $name, $value): void
    {
        $this->$name = $value;
    }

    public function __get(string $name)
    {
        return "";
    }
}