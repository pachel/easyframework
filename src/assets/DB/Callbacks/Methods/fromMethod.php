<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

use Pachel\EasyFrameWork\DB\callBacks\fromCallback;

trait fromMethod
{
    public function from():fromCallback{
        return $this->class->from();
    }
}