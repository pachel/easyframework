<?php
namespace Pachel\EasyFrameWork;
define("START_EFW",microtime(true));
session_start();
ob_start();

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
    protected $app;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function authorise($path):bool
    {
        if($path == "multiples"){
            return true;
        }
        return false;
    }
    public function dashboard($app,$category,$id){
       // echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
        $app->kex = $category."-".$id;

    }
    public function ss(){

    }
    public function dashboard2($app){
        //$t = new CodeFormater();
        //$this->app->code = $t->php(file_get_contents(__DIR__."/config/App.php"));
    }
    public function email_szinkron(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }
    public function layout(){
        echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
    }
    public function always(){
        $this->app->teszt = 2;
      //  echo debug_backtrace()[0]['class']."->".debug_backtrace()[0]['function']."();\n";
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
        return $this->app->env(null);
    }
    public function cli(){
        print_r(func_get_args());
    }
    public function api_key_check(){
        if(!isset($this->app->GET["apikey"]) || $this->app->GET["apikey"] != 15487){
            $this->app->send_error(403);
        }
    }
}

/*
$Base = Base::instance();
Base::instance()->config(__DIR__ . "/config/App.php");
*/
Base::instance()->config(__DIR__ . "/config/dev_App.php");

require __DIR__."/config/Routes.php";




$Base = Base::instance();
/*
$Base->DB->select(
    "asdas",
    "asd",
    [
        "data"=>"select *FROM anothertable WHERE akarmi",
        "as"=>"ize"
    ]
);
exit();*/
$Base->run();

