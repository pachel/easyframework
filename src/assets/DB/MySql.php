<?php

namespace Pachel\EasyFrameWork\DB;

use Pachel\EasyFrameWork\DB\callBacks\fromCallback;
use Pachel\EasyFrameWork\DB\callBacks\selectCallback;

class mySql
{

    public static $CONNECTED = false;


    public function __construct()
    {
        self::$CONNECTED = true;
    }

    /**
     * @param array|string ...$arguments
     * @return selectCallback
     */
    public function select(array|string ...$arguments): selectCallback
    {
        print_r($arguments);
        return new selectCallback($this);
    }

    public function fromString(){

    }
    public function fromName(){

    }
    protected function name(){

    }
    protected function from($table)
    {
        return new fromCallback($this);
    }

    protected function where()
    {

    }

    protected function orderby()
    {

    }

    protected function groupby()
    {

    }

    protected function limit()
    {

    }

    public function __destruct()
    {
        //TODO: Kapcsolatot bezárni
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }
    }
}