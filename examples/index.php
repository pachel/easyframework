<?php
use Pachel\EasyFrameWork;
USe Pachel\EasyFrameWork\Routing;
require_once __DIR__."/../vendor/autoload.php";

class SmallController{
    protected EasyFrameWork\Base $app;
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function dashboard(){
        $this->app->set("");
    }
}

$Base = EasyFrameWork\Base::instance();


$Base->set("GLOBAL.test",1);

Routing::get(SmallController::class,"dashboard")->layout();

