<?php


namespace Pachel\EasyFrameWork;


use Pachel\EasyFrameWork\Routing;


class Base extends Prefab
{

    /**
     * @var array $vars
     */
    private static array $vars;

    const VAR_READONLY = ["GET", "POST", "SERVER", "COOKIE", "SESSION", "FILES", "EFW","ROUTES","APP","MYSQL"];



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
    public function get($key)
    {
        if (!preg_match("/^((.*)\.(.+))|(.+)$/i", $key, $preg)) {
            throw new \Exception("Invalid key format!");
        }

        if (empty($preg[1])) {
            return self::$vars[strtoupper($preg[0])];
        } else {
            return self::$vars[strtoupper($preg[2])][strtoupper($preg[3])];
        }
        return null;
    }

    public function set($key, $value): void
    {
        if (preg_match("/^" . implode("|", self::VAR_READONLY) . ".*/i", $key) && !$this->from_myself()) {
            throw new \Exception("Readonly variables: " . $key);
        }
        if (!preg_match("/^((.*)\.(.+))|(.+)$/i", $key, $preg)) {
            throw new \Exception("Invalid key format!");
        }

        if (empty($preg[1])) {
            self::$vars[strtoupper($preg[0])] = $value;
        } else {
            self::$vars[strtoupper($preg[2])][strtoupper($preg[3])] = $value;
        }

    }

    public function run()
    {
        $route = Routing::matchroute();
        if($route){
            if(method_exists($route["object"][0],$route["object"][1])){

                $class = new $route["object"][0]($this);
                $method = $route["object"][1];
                $class->$method();
                eval('?><?php phpinfo();');
            }
            else{
                $this->set("STATUS",404);
            }
        }
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

    public function __call(string $name, array $arguments)
    {
        switch ($name) {
            case "set":
                $this->set("_USERSVARIABLES." . $arguments[0], $arguments[1]);
                break;
        }
    }

    public function reroute()
    {

    }

    private function setvars(): void
    {
        $this->set("GET", $_GET);
        $this->set("POST", $_POST);
        $this->set("SERVER", $_SERVER);
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
            $this->set($key, $item);
        }
    }
}

return Base::instance();