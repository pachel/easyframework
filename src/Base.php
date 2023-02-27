<?php


namespace Pachel\EasyFrameWork;





use Pachel\EasyFrameWork\Routing;


class Base extends Prefab
{

    /**
     * @var array $vars
     */
    private static array $vars;

    const VAR_READONLY = ["GET","POST","SERVER","COOKIES","SESSION"];


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
        
    }

    public function route(){

        return new Routing(func_get_args());
    }

    public function get()
    {

    }

    public function set(): void
    {
        //self::VAR_READONLY;
    }

    public function __call(string $name, array $arguments)
    {
        switch ($name) {
            case "set":
                $this->set("_USERSVARIABLES." . $arguments[0], $arguments[1]);
                break;
        }
    }
    public function reroute(){

    }

}
return Base::instance();