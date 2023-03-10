<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

use Pachel\EasyFrameWork\DB\callBacks\paramsCallback;

trait paramsMethod
{
    public function params(array $table):paramsCallback{
        return $this->class->params($table);
    }
}