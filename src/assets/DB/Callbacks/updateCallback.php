<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\updateMethods;

/**
 * @method setCallback __set
 */
final class updateCallback extends CallbackBase
{
    /**
     * @param array|object $data
     * @return setCallback
     */
    public function set($data):setCallback{
        return $this->class->set($data);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->class->nonameset($name,...$arguments);
    }
}