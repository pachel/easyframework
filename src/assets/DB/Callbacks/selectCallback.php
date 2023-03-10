<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
//use Pachel\EasyFrameWork\DB\callBacks\Methods\fromMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\idMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\whereMethod;

final class selectCallback extends CallbackBase
{
    use whereMethod;
    public function id(int $id):idCallback{
        return $this->class->id($id);
    }
}