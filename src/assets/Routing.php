<?php

namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\Callbacks\RouteMethodCallback;
use Pachel\EasyFrameWork\Route;

/**
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback get(string $path, array|string|object $object);
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback post(string $path, array|string|object $object);
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback cli(string $args, array|string|object $object);
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback postget(string $path, array|string|object $object);
 */
final class Routing extends Prefab
{
    public Routes $routes;

    private const
        METHOD_ALIASES = [
        "method" => ["get", "post", "cli", "postget"]
    ];
    private array $vars;
    public function __construct()
    {
        $this->routes = new Routes();
    }
    private function first(){
        /*
        foreach ($this->routes AS $index => $route){
            $this->routes[$index]->first = 0;
        }
        $this->routes[$this->routes->count()-1]->first = 1;
        */
        $count = $this->routes->count()-1;
        $route = $this->routes[$count];
        $this->routes->delete($count);
        $this->routes->pop($route);


    }
    private function method(): RouteMethodCallback
    {
        $arguments = func_get_args();
        $route = $this->get_data_from_arguments($arguments);
        $this->routes->push($route);
        $arguments = array_merge($arguments);
        return new RouteMethodCallback(...$arguments);
    }

    /**
     * @return void
     * @throws \Exception
     * @example get()->view("layout.php");
     */
    private function view(): void
    {
        $this->is_efw_configured();
        $args = func_get_args();
        if (count($args) != 4) {
            throw new \Exception(Messages::ROUTING_PARAMETER_MISSING);
        }
        $route = $this->get_data_from_arguments($args);
        $this->routes[$this->routes->count() - 1] = $route;
    }

    private function is_efw_configured()
    {
        if (!Base::instance()->env("EFW.CONFIGURED")) {
            throw new \Exception(Messages::BASE_APP_NOT_CONFIGURED);
        }
    }

    private function get_data_from_arguments($args)
    {
        $route = new Route([
            "path" => ($args[1] == "*" ? "*" : Functions::checkSlash2($args[1])),
            "method" => strtoupper($args[0]),
            "object" => $args[2]
        ]);
        //HA view, csak akkor kell a layout paraméter
        if (isset($args[3])) {
            $templatefile = Base::instance()->env("APP.VIEWS") . $args[3];
            if (!file_exists($templatefile)) {
                throw new \Exception(Messages::DRAW_TEMPLATE_NOT_FOUND);
            }
            $route["template"] = $templatefile;
            //HA view, csak akkor kell a layout paraméter
            $route["layout"] = $this->get_layout($templatefile);
        }
        $this->prepare_path_to_regex($route);
        return $route;
    }

    private function prepare_path_to_regex(Route &$route)
    {
        if ($route->path == "*") {
            $route->path_to_regex = ".*";
            return;
        }
        if (preg_match_all("/\{(.+?)\}/", $route->path, $preg)) {
            $route->url_variables = $preg[1];
            $route->path_to_regex = str_replace($preg[0], "##", $route->path);
        }
        //Minden regexes kifejezést ki kell iktatni a kereséshez
        $route->path_to_regex = preg_replace("/([\/\-\{\}\[\]\.\+\*\?\$\^\(\)])/", "\\\\$1", (empty($route->path_to_regex) ? $route->path : $route->path_to_regex));
        $route->path_to_regex = str_replace("##", "(.+)", $route->path_to_regex);
    }

    private function get_layout($template): string
    {
        $content = file_get_contents($template);
        if (preg_match("/<!\-\-layout:(.+)\-\->/i", $content, $preg)) {
            if (!is_file(Base::instance()->env("APP.VIEWS") . $preg[1])) {
                throw new \Exception("Layout not exists: " . Base::instance()->env("APP.VIEWS") . $preg[1]);
            }
            return Base::instance()->env("APP.VIEWS") . $preg[1];
        }
        return "";
    }

    public function get_request_method()
    {
        $method = Base::instance()->env("SERVER.REQUEST_METHOD");
        if (empty($method)) {
            return "CLI";
        }
        return $method;
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

    public function __call(string $name, array $arguments)
    {
        $name = $this->method_alias($name, $arguments);
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }

    }
    private function neg_uri($URI)
    {
        //TODO: MEg kell vizsgálni az URL-t is
        $URI = preg_replace("/\?.*$/", "", $URI);
        $full = Base::instance()->env("SERVER.REQUEST_SCHEME") . "://" . Base::instance()->env("SERVER.server_name") . $URI;
        $d = explode(Base::instance()->env("APP.URL"), $full);
        if (count($d) == 2) {
            $URI = Functions::checkSlash2($d[1]);
        }
        return $URI;
    }
    public function get_matches_routes():Routes{
        return $this->routes->matchesroutes();
    }
    public function generate_uri(): string
    {
        if (!isset(self::$VARS["ROUTE"]) || empty(self::$VARS["ROUTE"])) {

            $URI = Base::instance()->env("SERVER.REQUEST_URI");
            $argv = Base::instance()->env("SERVER.argv");
            if (empty($URI) && !empty($argv)) {
                $URI = $argv[1];
            } else {
                $URI = $this->neg_uri($URI);
            }
            $this->vars["uri"] = $URI;
            return $URI;
        } else {
            return $this->vars["uri"];
        }
    }
}