<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

use Pachel\EasyFrameWork\DB\callBacks\whereCallback;

trait whereMethod
{
    public function where(object|array $table):whereCallback{
        return $this->class->where($table);
    }
}