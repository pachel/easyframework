<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\updateMethods;

/**
 * @method setCallback __set
 */
final class insertCallback extends CallbackBase
{
    public function set(array|object $data){
        $this->class->set($data);
    }
}