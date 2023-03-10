<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\execMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\getMethods;


final class paramsCallback extends CallbackBase
{
    use execMethod;
    use getMethods;
}