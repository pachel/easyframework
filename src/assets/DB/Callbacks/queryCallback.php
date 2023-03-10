<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\execMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\getMethods;
use Pachel\EasyFrameWork\DB\callBacks\Methods\paramsMethod;


final class queryCallback extends CallbackBase
{
    use paramsMethod;
    use execMethod;
    use getMethods;
}