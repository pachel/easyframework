<?php

namespace Pachel\EasyFrameWork\DB\callBacks;

use Pachel\EasyFrameWork\Callbacks\CallbackBase;
use Pachel\EasyFrameWork\DB\callBacks\Methods\fromMethod;
use Pachel\EasyFrameWork\DB\callBacks\Methods\getMethods;
use Pachel\EasyFrameWork\DB\callBacks\Methods\whereMethod;

final class idCallback extends CallbackBase
{
    use getMethods;
}