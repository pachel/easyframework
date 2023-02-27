<?php


namespace Pachel\EasyFrameWork;

abstract class Prefab
{

    /**
     *    Return class instance
     * @return static
     **/
    static function instance()
    {
        if (!Registry::exists($class = get_called_class())) {
            $ref = new \ReflectionClass($class);
            $args = func_get_args();
            Registry::set($class,
                $args ? $ref->newinstanceargs($args) : new $class);
        }
        return Registry::get($class);
    }

}