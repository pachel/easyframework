<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\updateMethods;

/**
 * @method setCallback __set
 */
final class insertCallback extends CallbackBase
{
    /**
     * @param array|object $data
     * @return void
     */
    public function set($data){
        $this->class->set($data);
    }
}