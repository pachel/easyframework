<?php

namespace Pachel\EasyFrameWork\Callbacks;

use Pachel\EasyFrameWork\Routing;
use Pachel\EasyFrameWork\Traits\allowMethod;
use Pachel\EasyFrameWork\Traits\beforeMethod;
use Pachel\EasyFrameWork\Traits\firstMethod;
use Pachel\EasyFrameWork\Traits\jsonMethod;
use Pachel\EasyFrameWork\Traits\viewMethod;



final class RouteMethodCallback extends CallbackBase
{
    use viewMethod;
    use firstMethod;
    use jsonMethod;
    use beforeMethod;
    use allowMethod;
}

final class beforeMethodCallback extends CallbackBase
{
    use viewMethod;
    use firstMethod;
    use jsonMethod;
    use allowMethod;
}


/**
 * @method void onlyone();
 * @method void allow();
 * @method nameMethodCallback name(string $name_in_layout) Meg lehet nevezni a templatet és így nem kell a kódba ágyazni az azonosítót
 */
final class generateMethodCallback extends CallbackBase
{
}

/**
 * @method void onlyone()
 * @method void allow()
 * @method layoutMethodCallback layout(string $layout_path)
 */
final class nameMethodCallback extends CallbackBase
{
}

/**
 * @method void onlyone();
 * @method void allow();
 */
final class layoutMethodCallback extends CallbackBase
{
}