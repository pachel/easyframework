<?php

namespace Pachel\EasyFrameWork\Callbacks;

use Pachel\EasyFrameWork\Routing;

/**
 * @method void view(string $template_name);
 */
final class RouteMethodCallback extends CallbackBase
{
    protected $class;
    protected $arguments;
    public function __construct()
    {
        $this->arguments = func_get_args();
        $this->class = Routing::instance();
    }
}