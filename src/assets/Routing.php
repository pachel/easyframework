<?php

namespace Pachel\EasyFrameWork;

class Routing
{
    public function __construct()
    {

    }

    public static function get()
    {
        print_r(func_get_args());
    }

    public static function post()
    {

    }
}