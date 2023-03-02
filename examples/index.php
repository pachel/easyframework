<?php
session_start();
use Couchbase\View;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\Draw;
use Pachel\EasyFrameWork\Routing;
use Pachel\EasyFrameWork\Helpers\MethodInvoker;
use Pachel\EasyFrameWork\Routing2;

require_once __DIR__."/../vendor/autoload.php";
//requ
//ire_once __DIR__."/config/Routes.php";



/**
 * @method void view(string $name)
 */
class TestClass {
    public function __call(string $name, array $arguments)
    {
        $invoker = new MethodInvoker();
        $invoker->invoke(Routing::instance(),$name);
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

Routing::instance()->get("*",[SmallController::class,"layout"])->layout("layout.php");

Routing::instance()->get("dashboard",[SmallController::class,"dashboard"])->view("layout.index.php");

Routing::instance()->cli("email-szinkronok",function (){ echo 1; });


//$Base = Base::instance();
//$Base->run();


/*$Auth = \Pachel\EasyFrameWork\Auth::instance();

$Auth->authorise(function ($page){
    return true;
});*/
/*
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
*/
//$Base->run();