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

    public function dashboard($app,$category,$id){
        $app->sajatom = 2;
        echo $category."\n";
        echo $id."\n";
        echo $app->env("sajatom");


    }
    public function dashboard2(){
        echo "2";

    }
    public function email_szinkron(){
        echo "cli";
    }
    public function layout(){
        $this->app->env("layout",1);
    }

    /**
     * @param Pachel\EasyFrameWork\BaseAsArgument $app
     * @return void
     */
    public function landing($app){
        $routes = $app->get_loaded_routes();
        //print_r($routes);
        $app->cache->teszt = [1];


        print_r($app->cache->teszt);
    }
}
/*
$Base = Base::instance();
Base::instance()->config(__DIR__ . "/config/App.php");
*/
Base::instance()->config(__DIR__ . "/config/App.php");
Base::instance()->env("teszt",1);

Routing::instance()->get("*",function ($app){

})->first();
Routing::instance()->get("*",function ($app){
    //echo $app->teszt;
})->first();
Routing::instance()->get("*",function ($app){
    echo $app->teszt;
})->first();
Routing::instance()->get("",[SmallController::class,"landing"])->view("layout.index.php");
Routing::instance()->get("dashboard/login",[SmallController::class,"dashboard"])->view("login.php");
Routing::instance()->post("teszt",[SmallController::class,"dashboard"])->view("layout.index.php");
Routing::instance()->get("dashboard/{category}/{id}.html",[SmallController::class,"dashboard"])->view("layout.index.php");

Routing::instance()->cli("email-szinkronok",function (){ echo 1; });

//print_r(Routing::instance()->routes->matchesroutes());
$Base = Base::instance();
$Base->run();


/*$Auth = \Pachel\EasyFrameWork\Auth::instance();

$Auth->authorise(function ($page){
    return true;
});*/



//$Base->env("GT",1);

//$Base->run();