<?php

namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\Interfaces\Route;

final class Routing
{
    private static array $ROUTES = [];

    public function __construct()
    {

    }

    public static function get(string $path, array|string|object $object)
    {
        self::addroute($path, $object, "get");

    }
    public static function postget(string $path, array|string|object $object)
    {
        self::addroute($path, $object, "get|post");

    }

    public static function post(string $path, array|string|object $object)
    {
        self::addroute($path, $object, "post");
    }

    public static function cli(string $path, array|string|object $object)
    {
        self::addroute($path, $object, "cli");
    }
    public static function always(array|string|object $object)
    {
        self::addroute(".*", $object, "get|post");
    }

    private static function addroute($path, $object, $method)
    {
        $pathlenght = strlen($path);
        $object = self::cut_objectstring($object);
        $new = [
            "access" => true,
            "accesstype" => "F",
            "path" => $path,
            "object" => $object,
            "method" => strtoupper($method)
        ];
        self::$ROUTES[] = $new;

    }

    private static function cut_objectstring($object)
    {
        if(is_string($object)){
            if(preg_match("/^(.+)\->(.+)$/",$object,$preg)){
                return [
                  $preg[1],$preg[2]
                ];
            }
        }
        return $object;
    }

    public static function matchroute(): bool|array
    {
        usort(self::$ROUTES, [self::class, "sortroutes"]);
        $URI = Base::instance()->get("SERVER.REQUEST_URI");
        $argv = Base::instance()->get("SERVER.argv");
        if(empty($URI) && !empty($argv)){
            $URI = $argv[1];
        }
        foreach (self::$ROUTES as &$route) {
            if (preg_match(">^.*" . $route["path"] . "$>", $URI) && preg_match("/".self::get_request_method()."/i",$route["method"])) {
                return $route;
            }
        }
        return false;
    }
    private static function get_request_method(){
        $method = Base::instance()->get("SERVER.REQUEST_METHOD");

        if(empty($method)){
            return "CLI";
        }
        return $method;
    }
    private static function getmethod()
    {

    }

    private static function sortroutes($a, $b)
    {
        return strlen($b["path"]) - strlen($a["path"]);

    }
}