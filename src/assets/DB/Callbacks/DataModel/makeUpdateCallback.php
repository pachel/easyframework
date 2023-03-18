<?php

namespace Pachel\EasyFrameWork\DB\callBacks\DataModel;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;

final class makeUpdateCallback extends CallbackBase
{
    public function set($pdo_params):bool{
         return $this->class->set($pdo_params);
    }
}