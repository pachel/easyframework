<?php
session_start();
use Couchbase\View;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\Draw;
use Pachel\EasyFrameWork\Routing;

require_once __DIR__."/../vendor/autoload.php";
//require_once __DIR__."/config/Routes.php";
class SmallController{
    protected Base $app;
    public function __construct($app)
    {
        $this->app = $app;

    }

    public function dashboard(){
        $this->app->set("kex",1);
        Draw::template("layout.index.php");
    }
    public function dashboard2(){


    }
    public function email_szinkron(){
        echo "cli";
    }
    public function dashboard1(){

        $this->app->set("kex","DASHBOARD1");
        Draw::template("layout.index.php");
    }
}

Base::instance()->config(__DIR__ . "/config/App.php");
$Base = Base::instance();
$Auth = \Pachel\EasyFrameWork\Auth::instance();
$Auth->authorise(function ($page){
    echo "asdasd";
    return true;
});
Routing::get("/",[SmallController::class,"dashboard1"]);
Routing::postget("dashboard",[SmallController::class,"dashboard"]);
Routing::get("dashboard/teszt",[SmallController::class,"dashboard2"]);

Routing::cli("emailszinkron","SmallController->email_szinkron");
Routing::cli("run",function (){
    echo "run";
});

$Base->run();