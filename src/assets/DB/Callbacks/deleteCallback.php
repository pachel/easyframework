<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\fromMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\getMethods;
use Pachel\EasyFrameWork\DB\callBacks\Methods\idMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\whereMethod;

final class deletCallback extends CallbackBase
{
    public function where(...$arguments):void{
        $this->class->where(...$arguments);
    }
    public function id(int $id):void{
        $this->class->id($id);
    }
}