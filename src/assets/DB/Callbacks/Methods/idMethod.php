<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

use Pachel\EasyFrameWork\DB\callBacks\whereCallback;

trait idMethod
{
    public function id(int $id):void{
        $this->class->id($id);
    }
}