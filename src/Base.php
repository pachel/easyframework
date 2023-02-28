<?php


namespace Pachel\EasyFrameWork;


use Pachel\EasyFrameWork\Routing;


class Base extends Prefab
{

    /**
     * @var array $vars
     */
    private static array $vars;
    /**
     * Gyári változók, ezeken a felhasználó nem módosíthatja a $this->set() függvénnyel
     */
    const VAR_READONLY = ["GET", "POST", "SERVER", "COOKIE", "SESSION", "FILES", "EFW","ROUTES","APP","MYSQL","REDIS","STATUS"];
    /**
     * Ide azok a változók kerülnek majd, amiket gyorsítótárazni kell majd
     */
    const VAR_CACHE = "CACHE";
    /**
     * Azok a változók, ahol meg kell nézni, hogy a karakterlánc végén egy perjel legyen
     */
    const VAR_PATHS = ["app.ui","app.views","app.url","app.logs","app.temp"];
    /**
     * Ezeket a függvényeket kitiltjuk a temple fájlokból
     */
    //const DRAW_FUNCTIONS_FILTER = ["include(.+?)","require","require_once","fopen","file_get_content","file_put_content"];

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
        $this->setvars();

    }
    private function get($key)
    {
        if(empty($key)){
            return self::$vars;
        }
        if (!preg_match("/^((.*)\.(.+))|(.+)$/i", $key, $preg)) {
            throw new \Exception("Invalid key format!");
        }

        if (empty($preg[1])) {
            if(!isset(self::$vars[strtoupper($preg[0])]) && !isset(self::$vars[$preg[0]])){
                return null;
            }
            if($this->is_systemvarialbe($key)) {
                return self::$vars[strtoupper($preg[0])];
            }
            else{
                return self::$vars[$preg[0]];
            }
        } else {
            if(!isset(self::$vars[strtoupper($preg[2])][strtoupper($preg[3])]) && !isset(self::$vars[$preg[2]][$preg[3]])){
                return null;
            }
            if($this->is_systemvarialbe($key)) {
                return self::$vars[strtoupper($preg[2])][strtoupper($preg[3])];
            }
            else{
                return self::$vars[$preg[2]][$preg[3]];
            }
        }
        return null;
    }

    private function set($key, $value): void
    {
        if ($this->is_systemvarialbe($key) && !$this->from_myself()) {
            throw new \Exception("Readonly variables: " . $key);
        }
        if (!preg_match("/^((.*)\.(.+))|(.+)$/i", $key, $preg)) {
            throw new \Exception("Invalid key format!");
        }

        if (empty($preg[1])) {
            if ($this->is_systemvarialbe($key)) {
                self::$vars[strtoupper($preg[0])] = $value;
            }
            else{
                self::$vars[$preg[0]] = $value;
            }
        } else {
            if ($this->is_systemvarialbe($key)) {
                self::$vars[strtoupper($preg[2])][strtoupper($preg[3])] = $value;
            }
            else{
                self::$vars[$preg[2]][$preg[3]] = $value;
            }
        }

    }
    private function is_systemvarialbe($key){
        if (preg_match("/^" . implode("|", self::VAR_READONLY) . ".*/i", $key)) {
            return true;
        }
        return false;
    }
    private function is_path($key){
        if (preg_match("/^" . implode("|", self::VAR_PATHS) . ".*/i", $key)) {
            return true;
        }
        return false;
    }
    public function run()
    {
        $route = Routing::matchroute();
        $this->status(200);
        if($route){
            if($this->run_content($route)){

            }
        }
        else{
            $this->status(404);
        }
        if($this->status() != 200){

        }

        Draw::instance()->generate();
    }
    private function run_content($route)
    {
        if(is_array($route["object"]) && method_exists($route["object"][0],$route["object"][1])){
            $class = new $route["object"][0]($this);
            $method = $route["object"][1];
            $class->$method();

            return true;
        }
        elseif (is_object($route["object"])){
            $route["object"]();

            return true;
        }
        else{
            $this->status(404);
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function env(){
        $args = func_get_args();
        if(count($args)==1){
            return $this->get($args[0]);
        }
        elseif (count($args)==2){
            $this->set($args[0],$args[1]);
        }
        else{

        }
    }
    public function status(){
        $args = func_get_args();
        if(count($args)==0){
            return $this->get("STATUS");
        }
        $this->set("STATUS",$args[0]);

    }
    private function from_myself(): bool
    {
        foreach (debug_backtrace() as $item) {
            if ($item["function"] == "set" && preg_match("/Base\.php$/", $item["file"])) {
                return true;
            }
        }
        return false;
    }

    public function reroute($route)
    {
        header("location:".$this->get("APP.URL").$route);
        exit();
    }

    private function setvars(): void
    {
        $this->set("GET", $_GET);
        $this->set("POST", $_POST);
        foreach ($_SERVER AS $key=>$value){
            $s2[strtoupper($key)] = $value;
        }
        $this->set("SERVER", $s2);
        $this->set("SESSION", (isset($_SESSION) ? $_SESSION : null));
        $this->set("FILES", $_FILES);
        $this->set("COOKIE", $_COOKIE);
        $this->getserverurl();

    }

    private function getserverurl(): string
    {
        $self = (string)$this->get("SERVER.php_self");
      //  echo $self;
        return $self;
    }

    public function config($config): void
    {
        if (is_array($config)) {

        } elseif (is_file($config)) {
            $config = require $config;
        } else {
            throw new \Exception("Config error!");
        }
        foreach ($config as $key => $item) {
            if($this->is_path($key)){
                $item = Functions::checkSlash($item);
            }
            $this->set($key, $item);
        }
    }
}

return Base::instance();