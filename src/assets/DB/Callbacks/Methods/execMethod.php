<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;
trait execMethod
{
    public function exec():bool{
        return $this->class->exec();
    }
}