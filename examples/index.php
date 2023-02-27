<?php
namespace TDF;
use Pachel\EasyFrameWork;
USe Pachel\EasyFrameWork\Routing;
use Configs;
require_once __DIR__."/../vendor/autoload.php";

class SmallController{
    protected EasyFrameWork\Base $app;
    public function __construct($app)
    {
        $this->app = $app;

    }

    public function dashboard(){
        $s = $this->app->get("APP");
        print_r($s);
    }
}
EasyFrameWork\Base::instance()->config(__DIR__ . "/config/App.php");

$Base = EasyFrameWork\Base::instance();


echo $Base->get("TESZT");





$Base->run();