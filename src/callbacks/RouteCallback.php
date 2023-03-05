<?php

namespace Pachel\EasyFrameWork\Callbacks;

use Pachel\EasyFrameWork\Routing;

/**
 * @method generateMethodCallback view(string $template_name);
 * @method void first();
 * @method generateMethodCallback json();
 */
final class RouteMethodCallback extends CallbackBase{}


/**
 * @method void onlyone();
 */
final class generateMethodCallback extends CallbackBase{}