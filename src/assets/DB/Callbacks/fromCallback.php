<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\fromMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\whereMethod;

class fromCallback extends CallbackBase
{
    use whereMethod;
}