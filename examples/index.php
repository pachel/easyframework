<?php
session_start();

use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\Routing;
use Pachel\EasyFrameWork\Auth;

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
    /**
     * @var \Pachel\EasyFrameWork\BaseAsArgument $app;
     */
    protected  $app;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function authorise($path):bool
    {
        return true;
    }
    public function dashboard($app,$category,$id){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
        $app->kex = $category."-".$id;

    }
    public function dashboard2($app){
        //$this->app->cache->teszt = 1;

        echo "cache:".$this->app->cache->teszt."\n";
        echo "fromParam: ".$app->teszt;
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
        $app->kex = 1;


    }
    public function email_szinkron(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }
    public function layout(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }
    public function always(){
        $this->app->teszt = 2;
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }

    /**
     * @param Pachel\EasyFrameWork\BaseAsArgument $app
     * @return void
     */
    public function landing($app){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";

    }

    /**
     * @param Pachel\EasyFrameWork\BaseAsArgument $app
     * @return array
     */
    public function api($app){

        return $this->app->GET;
    }
}

/*
$Base = Base::instance();
Base::instance()->config(__DIR__ . "/config/App.php");
*/
Base::instance()->config(__DIR__ . "/config/App.php");
Routing::instance()->get("*",[SmallController::class,"always"])->first();
Routing::instance()->get("",[SmallController::class,"landing"])->view("layout.index.php");
Routing::instance()->get("dashboard/login",[SmallController::class,"dashboard3"])->view("login.php");
Routing::instance()->get("teszt",[SmallController::class,"dashboard2"])->view("layout.index.php");
Routing::instance()->get("dashboard/{category}/{id}.html",[SmallController::class,"dashboard"])->view("layout.index.php");
Routing::instance()->get("static.html")->view("layout.php");
Routing::instance()->get("login")->view("login.php");
Routing::instance()->get("ss","SmallController->dashboard2")->view("layout.index.php");
/**
 * Az api kéréseknél (POST|GET) csak ez az egy metódus fut le,
 * és egy JSON objektumot ad vissza a oldal
 */
Routing::instance()->postget("api.php",[SmallController::class,"api"])->json()->onlyone();
Routing::instance()->cli("email-szinkronok",function (){ echo 1; });

/**
 * Authorise
 */
Auth::instance()->policy("deny");
Auth::instance()->allow("login");
Auth::instance()->allow("api.php");
/**
 * Csak a POST|GET path-ra vonatkozik, a cli nincs ellenőrizve
 */
Auth::instance()->authorise([SmallController::class,"authorise"]);

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