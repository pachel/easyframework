<?php

namespace Pachel\EasyFrameWork;

/**
 * @author pachel82@gmail.com
 * @method Layout current()
 * @method Layout offsetGet(mixed $offset)
 * @method Layout[] search(array|string $search)
 * @method Layout[] match(array|string $search)
 */
class Layouts extends ListObject
{
    protected $class = Layout::class;
}

/**
 * @author pachel82@gmail.com
 * @property string path;
 * @property string template;
 * @property string method;
 * @property mixed object;
 */
final class Layout extends ListObjectItem {}
