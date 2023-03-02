<?php

namespace Pachel\EasyFrameWork\Callbacks;

use Pachel\EasyFrameWork\Helpers\MethodInvoker;

abstract class CallbackBase
{
    protected $class = "";
    protected $arguments;

    public function __call(string $name, array $arguments)
    {
        $arguments = array_merge($this->arguments, $arguments);
        return $this->class->$name(...$arguments);

    }
}