<?php

namespace Pachel\EasyFrameWork;

/**
 * @author pachel82@gmail.com
 * @method CacheObjectItem current()
 * @method CacheObjectItem offsetGet(mixed $offset)
 * @method CacheObjectItem[] search(array|string $search)
 * @method CacheObjectItem[] match(array|string $search)
 */
class CacheObject extends ListObject
{
    protected $class = CacheObjectItem::class;

}

/**
 * @property int timestamp
 * @property string content
 * @property int expires
 * @property string name
 */
class CacheObjectItem extends ListObjectItem
{

}