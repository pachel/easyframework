<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\fromMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\getMethods;
use Pachel\EasyFrameWork\DB\callBacks\Methods\whereMethod;

final class fromCallback extends CallbackBase
{
    use whereMethod;
    use getMethods;
}