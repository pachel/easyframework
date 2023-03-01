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
        $this->app->env("kex",1);
        Draw::template("layout.index.php");
    }
    public function dashboard2(){
        echo "2";
        Draw::template("layout.index.php");
    }
    public function email_szinkron(){
        echo "cli";
    }
    public function layout(){
        $this->app->env("layout",1);
    }
    public function landing(){

        $this->app->env("kex","DASHBOARD1");
        Draw::template("layout.index.php");
    }
}

Base::instance()->config(__DIR__ . "/config/App.php");
$Base = Base::instance();
$Auth = \Pachel\EasyFrameWork\Auth::instance();

$Auth->authorise(function ($page){
    return true;
});

Routing::get("/",[SmallController::class,"landing"]);
Routing::postget("dashboard",[SmallController::class,"dashboard"]);
Routing::get("dashboard/teszt",[SmallController::class,"dashboard2"]);

Routing::cli("emailszinkron","SmallController->email_szinkron");
Routing::cli("run",function (){
    echo "run";
});

Routing::layout(".*",[SmallController::class,"layout"],"layout.php");

$Base->run();