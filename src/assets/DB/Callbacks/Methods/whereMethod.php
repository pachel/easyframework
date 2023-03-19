<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

use Pachel\EasyFrameWork\DB\callBacks\whereCallback;

trait whereMethod
{
    /**
     * @param object|array $where Feltétel
     * @return whereCallback
     */
    public function where($where):bool{
        return $this->class->where($where);
    }
}