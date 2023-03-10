<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\fromMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\updateMethods;

final class setCallback extends CallbackBase
{
    use updateMethods;
    public function __call(string $name, array $arguments)
    {
        $this->class->nonamewhere($name,...$arguments);
    }
}