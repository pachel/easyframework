<?php

namespace Pachel\EasyFrameWork\DB\callBacks\DataModel;




use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\whereMethod;

final class updateCallback extends CallbackBase
{
    use whereMethod;
    public function __call(string $name, array $arguments)
    {
        return $this->class->nonamewhere($name,...$arguments);
    }
}