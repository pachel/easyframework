<?php
session_start();
use Couchbase\View;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\Draw;
use Pachel\EasyFrameWork\Routing;

require_once __DIR__."/../vendor/autoload.php";
//requ
//ire_once __DIR__."/config/Routes.php";


class TestClass {
    private function privateMethod(string $txt) {
        print_r('invoked privateMethod: ' . $txt);
    }
}

//(new MethodInvoker)->invoke(new TestClass, 'privateMethod', ['argument_1']);

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

/*$Auth = \Pachel\EasyFrameWork\Auth::instance();

$Auth->authorise(function ($page){
    return true;
});*/

Routing::get("/",function (){
    echo "sa";
    Draw::template("layout.index.php");
});

Routing::postget("dashboard",[SmallController::class,"dashboard"]);
Routing::get("dashboard/teszt",[SmallController::class,"dashboard2"]);
Routing::get("teszt",[SmallController::class,"dashboard2"]);

Routing::cli("emailszinkron","SmallController->email_szinkron");
Routing::cli("run",function (){
    echo "run";
});

Routing::layout(".*",[SmallController::class,"layout"],"layout.php");

$Base->env("GT",1);
$Base->run();