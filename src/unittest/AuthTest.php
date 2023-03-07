<?php

namespace Pachel\EasyFrameWork\Tests;

use Pachel\EasyFrameWork\MethodAlias;
use Pachel\EasyFrameWork\returnObjectArray;

class AuthTest extends \Pachel\EasyFrameWork\Auth
{
    use MethodAlias;
    use returnObjectArray;
    use GetSetProperties;
    public function tesztFalse(){
        return false;
    }
    public function tesztTrue(){
        return true;
    }
}