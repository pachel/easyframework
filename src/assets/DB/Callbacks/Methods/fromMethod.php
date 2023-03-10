<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

use Pachel\EasyFrameWork\DB\callBacks\fromCallback;

trait fromMethod
{
    /**
     * @param array|string ...$arguments
     * @return fromCallback
     */
    public function from(...$arguments):fromCallback{
        return $this->class->from(...$arguments);
    }
}