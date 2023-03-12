<?php

namespace Pachel\EasyFrameWork\Tests;

class MySqlTest extends \Pachel\EasyFrameWork\DB\mySql
{
    use GetSetProperties;
    public function tesztFalse(){
        return false;
    }
    public function tesztTrue(){
        return true;
    }
}