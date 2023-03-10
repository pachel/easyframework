<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

use Pachel\EasyFrameWork\DB\callBacks\whereCallback;

trait whereMethod
{
    /**
     * @param object|array $table
     * @return whereCallback
     */
    public function where($table):whereCallback{
        return $this->class->where($table);
    }
}