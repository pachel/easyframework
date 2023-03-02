<?php

namespace Pachel\EasyFrameWork;

/**
 * @author pachel82@gmail.com
 * @method Route current()
 * @method Route offsetGet(mixed $offset)
 * @method Route[] search(array|string $search)
 * @method Route[] match(array|string $search)
 */
class Routes extends ListObject
{
    protected $class = Route::class;
}

/**
 * @author pachel82@gmail.com
 * @property string path;
 * @property string template;
 * @property string layout;
 * @property string method;
 * @property mixed object;
 */
final class Route extends ListObjectItem {}
