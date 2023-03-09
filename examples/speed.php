<?php
$max = 200;

$url = "http://localhost/easyframe/examples/layout";
//$url = "http://localhost/tdf-persons/gyartas/teszt";
//$url = "http://localhost/easyframe/examples/layoutasjkdhaks";
//$url = "http://localhost/laravel/public/uziufds";

$name = md5($url);
for ($x=1;$x<=$max;$x++){
    $start = microtime(true);
    $f = @file_get_contents($url);
    file_put_contents(__DIR__."/temp/requesttime_".$name.".log",round((microtime(true)-$start),4)." ".$url."\n",FILE_APPEND);
    usleep(10);
}

