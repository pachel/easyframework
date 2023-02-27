<?php

namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\Interfaces\Route;

final class Routing
{
    private static array $ROUTES = [];

    public function __construct()
    {

    }

    public static function get(string $path, array $object)
    {
        self::addroute($path, $object, "get");
    }

    public static function post(string $path, array $object)
    {
        self::addroute($path, $object, "post");
    }

    public static function cli(string $path, array $object)
    {
        self::addroute($path, $object, "cli");
    }

    private static function addroute($path, $object, $method)
    {
        self::$ROUTES[] = [
            "path" => $path,
            "object" => $object,
            "method" => strtoupper($method)
        ];
    }

    public static function matchroute(): bool|array
    {

        foreach (self::$ROUTES as $route) {
            echo Base::instance()->get("SERVER.REQUEST_METHOD") . "\n";
            echo $route["path"] . "\n";
            if (preg_match(">^.+" . $route["path"] . "$>", Base::instance()->get("SERVER.REQUEST_URI")) && Base::instance()->get("SERVER.REQUEST_METHOD") == $route["method"]) {
                return $route;
            }
        }
        return false;
    }

    private static function getmethod()
    {

    }
}