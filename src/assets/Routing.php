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
    public Layouts $layouts;

    private const
        METHOD_ALIASES = [
        "method" => ["get", "post", "cli", "postget"]
    ];

    public function __construct()
    {
        $this->routes = new Routes();
        $this->layouts = new Layouts();
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
        $this->routes[count($this->routes)] = $route;
        //$this->routes->push($route);
    }
    private function is_efw_configured(){
        if (!Base::instance()->env("EFW.CONFIGURED")) {
            throw new \Exception(Messages::BASE_APP_NOT_CONFIGURED);
        }
    }
    private function get_data_from_arguments($args)
    {
      $route = [
            "path" => Functions::checkSlash($args[1]),
            "object" => $args[2],
            "method" => strtoupper($args[0]),
        ];
        /**
         * HA view, csak akkor kell a layout paraméter
         */
        if (count($args) >= 4) {
            $templatefile = Base::instance()->env("APP.VIEWS") . $args[4];
            if (!file_exists($templatefile)) {
                throw new \Exception(Messages::DRAW_TEMPLATE_NOT_FOUND);
            }
            $route["template"] = $this->get_layout($templatefile);

            /**
             * HA view, csak akkor kell a layout paraméter
             */
            if (count($args) == 5) {
                $route["layout"] = $this->get_layout($templatefile);
            }
        }
        return $route;
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
}