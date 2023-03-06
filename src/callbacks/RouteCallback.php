<?php

namespace Pachel\EasyFrameWork\Callbacks;

use Pachel\EasyFrameWork\Routing;

/**
 * @method generateMethodCallback view(string $template_name);
 * @method void first();
 * @method generateMethodCallback json();
 * @method generateMethodCallback pdf();
 * @method beforeMethodCallback before($runnable_srcipt);
 */
final class RouteMethodCallback extends CallbackBase{}

/**
 * @method generateMethodCallback view(string $template_name);
 * @method void first();
 * @method generateMethodCallback json();
 * @method generateMethodCallback pdf();
 */
final class beforeMethodCallback extends CallbackBase{}


/**
 * @method void onlyone();
 */
final class generateMethodCallback extends CallbackBase{}