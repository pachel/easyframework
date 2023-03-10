<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\updateMethods;


class deleteCallback extends CallbackBase
{
   use updateMethods;
   public function __call(string $name, array $arguments)
   {
       $this->class->nonamedelete($name,...$arguments);
   }
}