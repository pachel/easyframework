<?php


namespace Pachel\EasyFrameWork;

//use Pachel\EasyFrameWork\Interfaces\Route;

final class Routing2
{
    private static array $ROUTES = [], $LAYOUT = [], $VARS = [];

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

    public static function layout(string $path, array|string|object $object, string $layout)
    {
        self::$LAYOUT[] = [
            "path" => $path,
            "layout" => $layout,
            "object" => $object
        ];
    }

    public static function getlayout()
    {
        if (!empty(self::$LAYOUT)) {
            return self::$LAYOUT[0];
        }
        return null;
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
        if (is_string($object)) {
            if (preg_match("/^(.+)\->(.+)$/", $object, $preg)) {
                return [
                    $preg[1], $preg[2]
                ];
            }
        }
        return $object;
    }

    public static function get_actual_route()
    {
        if (!isset(self::$VARS["ROUTE"]) || empty(self::$VARS["ROUTE"])) {

            $URI = Base::instance()->env("SERVER.REQUEST_URI");
            $argv = Base::instance()->env("SERVER.argv");
            if (empty($URI) && !empty($argv)) {
                $URI = $argv[1];
            } else {
                $URI = self::neg_uri($URI);
            }
            self::$VARS["ROUTE"] = $URI;
            return $URI;
        } else {
            return self::$VARS["ROUTE"];
        }
    }

    public static function matchroute(): bool|array
    {
        usort(self::$ROUTES, [self::class, "sortroutes"]);
        $URI = self::get_actual_route();

        foreach (self::$ROUTES as &$route) {
            $route["path"] = Functions::checkSlash($route["path"]);
            if (preg_match(">^" . $route["path"] . "$>", preg_replace("#([^:])//#", "$1/", $URI)) && preg_match("/" . self::get_request_method() . "/i", $route["method"])) {
                return $route;
            }
        }
        return false;
    }

    private static function neg_uri($URI)
    {
        //TODO: MEg kell vizsgálni az URL-t is
        $URI = preg_replace("/\?.*$/", "", $URI);
        $full = Base::instance()->env("SERVER.REQUEST_SCHEME") . "://" . Base::instance()->env("SERVER.server_name") . $URI;
        $d = explode(Base::instance()->env("APP.URL"), $full);
        if (count($d) == 2) {
            $URI = Functions::checkSlash($d[1]);
        }
        return $URI;
    }

    private static function get_request_method()
    {
        $method = Base::instance()->env("SERVER.REQUEST_METHOD");
        if (empty($method)) {
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