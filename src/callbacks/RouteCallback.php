<?php

namespace Pachel\EasyFrameWork\Callbacks;

use Pachel\EasyFrameWork\Routing;


/**
 * @method void first();
 * @method generateMethodCallback json();
 * @method generateMethodCallback pdf();
 * @method beforeMethodCallback before($runnable_srcipt);
 */
final class RouteMethodCallback extends CallbackBase
{

    /**
     * Itt kell beállítani a template állományt, pontosan meg kell adni
     * a template elérési útvonalát az APP.VIEWS mappán belül
     * @see https://github.com/pachel/easyframework#routing
     * @param string $template_name
     * @return generateMethodCallback
     */
    public function view(string $template_name): generateMethodCallback
    {
        return $this->class->view($template_name);
    }
}

/**
 * @method generateMethodCallback view(string $template_name);
 * @method void first();
 * @method generateMethodCallback json();
 * @method generateMethodCallback pdf();
 */
final class beforeMethodCallback extends CallbackBase
{
}


/**
 * @method void onlyone();
 * @method nameMethodCallback name(string $name_in_layout) Meg lehet nevezni a templatet és így nem kell a kódba ágyazni az azonosítót
 */
final class generateMethodCallback extends CallbackBase
{
}

/**
 * @method void onlyone();
 * @method layoutMethodCallback layout(string $layout_path);
 */
final class nameMethodCallback extends CallbackBase
{
}

/**
 * @method void onlyone();
 */
final class layoutMethodCallback extends CallbackBase
{
}