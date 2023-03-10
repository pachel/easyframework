<?php
namespace Pachel\EasyFrameWork\Tests;

use Pachel\EasyFrameWork\MethodAlias;

class RoutingTest extends \Pachel\EasyFrameWork\Routing{
    use MethodAlias;
    /*
    public function __call(string $name, array $arguments)
    {
        if (!method_exists($this, $name))
            throw new BadMethodCallException("method '$name' does not exist");
        return call_user_func_array(array($this, $name), $arguments);

    }*/
    public function __get(string $name)
    {
        return $this->{$name};
    }
}