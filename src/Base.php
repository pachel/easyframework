<?php


namespace Pachel\EasyFrameWork;


use Pachel\EasyFrameWork\Routing;

//set_error_handler("Pachel\\EasyFrameWork\\errorHandler");
set_exception_handler("Pachel\\EasyFrameWork\\exceptionHandler");
class Base extends Prefab
{

    /**
     * @var array $vars
     */
    private static $vars;

    private const CACHE_DIR = __DIR__."/../tmp/cache/";

    private const CONFIG_REQUIREMENT = ["APP.URL","APP.UI","APP.VIEWS","APP.LOGS"];
    /**
     * Gyári változók, ezeken a felhasználó nem módosíthatja a $this->set() függvénnyel, ezek kulcsai mindig nagybetűssé lesznek alakítva
     */
    const VAR_READONLY = ["SERVER", "COOKIE", "EFW", "ROUTES", "APP", "MYSQL", "REDIS", "STATUS"];
    /**
     * Ide azok a változók kerülnek majd, amiket gyorsítótárazni kell majd
     */
    const VAR_CACHE = "CACHE";

    /**
     * @var Cache $cache
     */
    public $cache;
    /**
     * Azok a változók, ahol meg kell nézni, hogy a karakterlánc végén egy perjel legyen
     */
    const VAR_PATHS = ["app.ui", "app.views", "app.url", "app.logs"];
    /**
     * Ezeket a függvényeket kitiltjuk a temple fájlokból
     */
    //const DRAW_FUNCTIONS_FILTER = ["include(.+?)","require","require_once","fopen","file_get_content","file_put_content"];
    /**
     * @var Routes $routes
     */
    private $routes;
    /**
     * PHP 5 allows developers to declare constructor methods for classes.
     * Classes which have a constructor method call this method on each newly-created object,
     * so it is suitable for any initialization that the object may need before it is used.
     *
     * Note: Parent constructors are not called implicitly if the child class defines a constructor.
     * In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * param [ mixed $args [, $... ]]
     * @link https://php.net/manual/en/language.oop5.decon.php
     */
    public function __construct()
    {
        $this->set("EFW.configured",false);
    }

    private function get($key,$fromself = true)
    {
        if (empty($key)) {
            return self::$vars;
        }
        if (!preg_match("/^((.*)\.(.+))|(.+)$/i", $key, $preg)) {
            new \Exception("Invalid key format!",100);
        }

        if (empty($preg[1])) {
            if (!isset(self::$vars[strtoupper($preg[0])]) && !isset(self::$vars[$preg[0]])) {
                return "";
            }
            if ($this->is_systemvarialbe($key)) {
                return self::$vars[strtoupper($preg[0])];
            } else {
                return self::$vars[$preg[0]];
            }
        } else {
            if (!isset(self::$vars[strtoupper($preg[2])][strtoupper($preg[3])]) && !isset(self::$vars[$preg[2]][$preg[3]])) {
                return "";
            }
            if ($this->is_systemvarialbe($key)) {
                return self::$vars[strtoupper($preg[2])][strtoupper($preg[3])];
            } else {
                return self::$vars[$preg[2]][$preg[3]];
            }
        }
        return "";
    }

    private function set($key, $value,$fromself = true): void
    {
        if ($this->is_systemvarialbe($key) && !$fromself) {
            throw new \Exception("Readonly variables: " . $key);
        }
        if (!preg_match("/^((.*)\.(.+))|(.+)$/i", $key, $preg)) {
            throw new \Exception("Invalid key format!");
        }
//        echo $key."\n";
        if (empty($preg[1])) {
            if ($this->is_systemvarialbe($key)) {
                if(is_array($value)){
                    $v2 = [];
                    foreach ($value AS $k => $item){
                        $v2[strtoupper($k)] = $item;
                    }
                    $value = $v2;
                }
                self::$vars[strtoupper($preg[0])] = $value;
            } else {
                self::$vars[$preg[0]] = $value;
            }
        } else {
            if ($this->is_systemvarialbe($key)) {
                self::$vars[strtoupper($preg[2])][strtoupper($preg[3])] = $value;
            } else {
                self::$vars[$preg[2]][$preg[3]] = $value;
            }
        }

    }

    private function is_systemvarialbe($key)
    {

        if (preg_match("/^" . implode("|", self::VAR_READONLY) . ".*/i", $key)) {
            return true;
        }
        return false;
    }

    private function is_path($key)
    {

        if (preg_match("/^" . implode("|", self::VAR_PATHS) . ".*/i", $key)) {
            return true;
        }
        return false;
    }

    public function run()
    {
        if (!$this->env("EFW.CONFIGURED")){
            throw new \Exception(Messages::BASE_APP_NOT_CONFIGURED);
        }

        $this->routes = Routing::instance()->get_matches_routes();

        /**
         * Ha az azonosítás sikertelen, akkor küldünk egy
         */
        if(!Auth::instance()->is_authorised($this->routes->find("path")->notequal("*")->get())){
            $this->send_error(403);
        }
        else {

            /*
            foreach ($this->routes AS $route){
                echo $route->path."\n";
                if($route->onlyone){
                    echo "oo:"."1";
                }
            }*/

            $this->run_all_routes();
            $View = new View($this->routes);
            if(!$View->show() && Routing::instance()->get_request_method()!="CLI"){
                $this->send_error(404);
            }
        }

    }
    public function send_error(int $code){
        ob_clean();
        $status =  Functions::HTTPStatus($code);
        header($status["error"]);
        exit();
    }
    private function run_all_routes(){

        $torun = $this->routes->find("onlyone")->equal(true)->get();
        if(!empty($torun)){
            /**
             * Csak azokat futtatjuk, ahol az onlyone paraméter be lettállítva
             */
            $this->run_routes($torun);
            $this->set("EFW.onlyone",true);

        }
        else {
            /**
             * Csak azoknak a rootoknak a futtatása, amikhez nincs template
             */
            $torun = $this->routes->search(["template" => ""]);
            $this->run_routes($torun);
            /**
             * A template-es rootok futtatása
             */
            $torun = $this->routes->search(["template" => ""], Routes::SEARCH_NOT_EQUAL);
            $this->run_routes($torun);
            $this->set("EFW.onlyone",false);
        }
    }

    /**
     * @param Route[] $torun
     * @return void
     */
    private function run_routes(&$torun){
        foreach ($torun AS &$item){
            $this->run_content($item);
        }
    }
    /**
     * @param Route $route
     * @return bool
     */
    use returnObjectArray;
    private function run_content(&$route)
    {
        /**
         * @var Route $route
         */
        if(empty($route)){
            return false;
        }

        if(is_array($route->url_variables)) {
            $arguments = array_merge([$this], $route->url_variables);
        }
        else{
            $arguments = [$this];
        }
        if($route->before != ""){
            $this->run_only_functions($route,$arguments,"before");
        }


        $this->run_only_functions($route,$arguments);
        /*
        if (is_object($route->object)) {
            $return = call_user_func($route->object,$this);
            $this->routes->find("path")->equal($route->path)->set(["return"=>$return]);
            return true;
        } elseif (is_array($route->object) && method_exists($route->object[0], $route->object[1])) {
            $class = new $route->object[0]($this);
            $method = $route->object[1];
            $return = $class->{$method}(...$arguments);
            $this->routes->find("path")->equal($route->path)->set(["return"=>$return]);
            return true;
        } else {
            if(preg_match("/(.+)\->(.+)/",$route->object,$preg)){
                $route->object = [$preg[1],$preg[2]];
                $this->run_content($route);
            }
            $this->status(404);
            return false;
        }*/
    }
    private function run_only_functions(&$route,$arguments,$object_name = "object"){
        $object = $this->get_object($route->{$object_name});
        if(empty($object)){
            return false;
        }
        /**
         * HA osztályt hívunk meg
         */
        if (!empty($object->className)){
            $classname = $object->className;
            $class = new $classname($this);
            $return = $class->{$object->methodName}(...$arguments);
            $this->routes->find("path")->equal($route->path)->set(["return"=>$return]);
            return true;
        }
        /**
         * Névtelen függvény hívása
         */
        elseif (!empty($object->object)){
            $return = call_user_func_array($object->object,$arguments);
            $this->routes->find("path")->equal($route->path)->set(["return"=>$return]);
            return true;
        }
        return false;
    }
    /**
     * @return mixed
     */
    public function env()
    {
        $args = func_get_args();
        if (count($args) == 1) {
            return $this->get($args[0]);
        } elseif (count($args) == 2) {
            $this->set($args[0], $args[1],false);
        } else {

        }
    }

    public function status()
    {
        $args = func_get_args();
        if (count($args) == 0) {
            return $this->get("STATUS");
        }
        $this->set("STATUS", $args[0]);

    }



    public function reroute($route)
    {
        header("location:" . $this->get("APP.URL") . $route);
        exit();
    }

    private function setvars(): void
    {
        $this->set("GET", $_GET);
        $this->set("POST", $_POST);
        foreach ($_SERVER as $key => $value) {
            $s2[strtoupper($key)] = $value;
        }
        $this->set("SERVER", $s2);
        $this->set("SESSION", (isset($_SESSION) ? $_SESSION : null));
        $this->set("FILES", $_FILES);
        $this->set("COOKIE", $_COOKIE);

        //Routing::get_actual_route();
        //echo 1;

    }

    private function getserverurl(): string
    {
        $self = (string)$this->get("SERVER.php_self");

        return $self;
    }

    public function config($config): void
    {
        if (is_array($config)) {

        } elseif (is_file($config)) {
            $config = require $config;
        } else {
            throw new \Exception(Messages::BASE_CONFIG_NOT_VALID);
        }



        foreach ($config as $key => &$item) {
            foreach ($item AS $key2 =>&$item2) {
                if ($this->is_path($key.".".$key2)) {
                    $item2 = Functions::checkSlash($item2);
                    if (!is_dir($item2) && $key2!="URL") {
                        throw new \Exception(Messages::BASE_FOLDER_NOT_EXISTS);
                    }
                }

            }
            $this->set($key, $item);
        }
        foreach (self::CONFIG_REQUIREMENT AS $item){
            if($this->get($item) == ""){
                throw new \Exception(Messages::BASE_CONFIG_MISSING_REQ);
            }
        }
        $this->setvars();
        $this->cache = new Cache(self::CACHE_DIR);
        $this->set("EFW.configured",true);
    }
    public function get_loaded_routes():Routes{
        return $this->routes;
    }
    public function __set(string $name, $value): void
    {
        $this->env($name,$value);
    }
    public function __get(string $name)
    {

        return $this->env($name);
    }

}

/**
 * @method void reroute(string $path);
 * @method mixed env(string $name,mixed $value);
 * @method Routes get_loaded_routes();
 * @method void send_error(int $code);
 * @property  array POST;
 * @property  array GET;
 * @property  array SESSION;
 * @property  array SERVER;
 * @property  Cache $cache;
 */
abstract class BaseAsArgument{}
//return Base::instance();