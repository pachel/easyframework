<?php

namespace Pachel\EasyFrameWork\Tests;

use Pachel\EasyFrameWork\DB\callBacks\selectCallback;
use Pachel\EasyFrameWork\DB\Models\Query;

class MySqlTest extends \Pachel\EasyFrameWork\DB\mySql
{
    use GetSetProperties;
    //public Query $QUERY;
    /*
    public function arrayFromObject($data){
        //sleep(1);
        //return parent::arrayFromObject($data);
    }*/


    public function tesztFalse(){
        return false;
    }
    public function tesztTrue(){
        return true;
    }
}