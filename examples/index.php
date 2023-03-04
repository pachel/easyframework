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

//$s = new TestClass();

//exit();


//(new MethodInvoker)->invoke(new TestClass, 'privateMethod', ['argument_1']);

class SmallController{
    protected Base $app;
    public function __construct($app)
    {
        $this->app = $app;

    }

    public function dashboard($app,$category,$id){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
        $app->kex = $category."-".$id;

    }
    public function dashboard2($app){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
        $app->kex = 1;


    }
    public function email_szinkron(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }
    public function layout(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }

    /**
     * @param Pachel\EasyFrameWork\BaseAsArgument $app
     * @return void
     */
    public function landing($app){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";

    }
}

/*
$Base = Base::instance();
Base::instance()->config(__DIR__ . "/config/App.php");
*/
Base::instance()->config(__DIR__ . "/config/App.php");
Base::instance()->env("teszt",1);

Routing::instance()->get("*",function ($app){
    //echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();";
    //echo "get all;\n";
    //print_r(Routing::instance()->routes);
})->first();

Routing::instance()->get("",[SmallController::class,"landing"])->view("layout.index.php");

Routing::instance()->get("dashboard/login",[SmallController::class,"dashboard3"])->view("login.php");
Routing::instance()->get("teszt",[SmallController::class,"dashboard2"])->view("layout.index.php");

Routing::instance()->get("dashboard/{category}/{id}.html",[SmallController::class,"dashboard"])->view("layout.index.php");
Routing::instance()->get("static.html")->view("layout.php");
Routing::instance()->get("login")->view("login.php");

Routing::instance()->cli("email-szinkronok",function (){ echo 1; });

//print_r(Routing::instance()->routes[0]->layout);
$Base = Base::instance();
$Base->run();

$item = new \Pachel\EasyFrameWork\Route(["layout"=>1]);




/*$Auth = \Pachel\EasyFrameWork\Auth::instance();

$Auth->authorise(function ($page){
    return true;
});*/



//$Base->env("GT",1);

//$Base->run();