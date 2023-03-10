<?php
use PHPUnit\Framework\TestCase;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\Messages;
use Pachel\EasyFrameWork\Helpers\MethodInvoker;
use Pachel\EasyFrameWork\testConfig;
class RoutingTest extends TestCase
{
    /**
     * @covers
     * @return void
     */
    public function testmethod(){
        $funct = function (){echo 1;};
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        \Pachel\EasyFrameWork\Routing::instance()->get("emailteszt/ablak/","Teszt->teszt")->view("login.php");
        \Pachel\EasyFrameWork\Routing::instance()->post("emailteszt/ajto",$funct);
        \Pachel\EasyFrameWork\Routing::instance()->cli("emailteszt",$funct);
        /**
         * @var \Pachel\EasyFrameWork\Routes $routes;
         * @var \Pachel\EasyFrameWork\Route $actual;
         */
        $routes = (new MethodInvoker)->invokeconstant(\Pachel\EasyFrameWork\Routing::instance(), 'routes');

        /**
         * GET
         */
        $actual = $routes->find("path")->equal("emailteszt/ablak")->get()[0];
        $this->assertEquals("emailteszt/ablak",$actual->path,"Path mentése GET");
        $this->assertEquals("Teszt->teszt",$actual->object,"Object mentése GET");
        $this->assertEquals(Base::instance()->env("app.views")."login.php",$actual->template,"Template mentése GET");
        $this->assertEquals("GET",$actual->method,"Method GET");

        /**
         * POST
         */

        $actual = $routes->find("path")->equal("emailteszt/ajto")->get()[0];
        $this->assertEquals("emailteszt/ajto",$actual->path,"Path mentése POST");
        $this->assertEquals($funct,$actual->object,"Object mentése POST");
        $this->assertEquals("",$actual->template,"Template mentése POST");
        $this->assertEquals("POST",$actual->method,"Method POST");

        /**
         * CLI
         */

        $actual = $routes->find("path")->equal("emailteszt")->get()[0];
        $this->assertEquals("emailteszt",$actual->path,"Path mentése CLI");
        $this->assertEquals($funct,$actual->object,"Object mentése CLI");
        $this->assertEquals("",$actual->template,"Template mentése CLI");
        $this->assertEquals("CLI",$actual->method,"Method CLI");
    }
    /**
     * @covers
     * @return void
     */
    public function testonlyone(){
        $_SERVER["REQUEST_URI"] = testConfig::dir."/examples/teszt";
        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();
        $Routingtest->routes->push(["path"=>"now"]);
        $Routingtest->generate("get")->onlyone();
        $actual = $Routingtest->routes[0]->direct;
        $this->assertEquals("get",$actual);
        $actual = $Routingtest->routes[0]->path;
        $this->assertEquals("now",$actual);
        $actual = $Routingtest->routes[0]->onlyone;
        $this->assertTrue($actual);
    }
    /**
     * @covers
     * @return void
     */
    public function testfirst(){
        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();
        $Routingtest->get("teszt2","teszt->teszt");
        $Routingtest->get("teszt","teszt->teszt")->first();

        $route = $Routingtest->routes->find("path")->equal("teszt")->onlyindex()[0];
        $this->assertEquals(0,$route);
    }
    /**
     * @covers
     * @return void
     */
    public function testbefore(){
        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();
        $Routingtest->get("teszt2","teszt->teszt");
        $Routingtest->get("teszt","teszt->teszt")->before("teszt");
        $route = $Routingtest->routes->find("path")->equal("teszt")->get()[0];
        $this->assertEquals("teszt",$route->before);

        $route = $Routingtest->routes->find("path")->equal("teszt2")->get()[0];
        $this->assertEquals("",$route->before);
    }
    /**
     * @covers
     * @return void
     */
    public function testview(){

        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();
        $Routingtest->get("teszt1","teszt->teszt")->view("layout.index.php");
        $Routingtest->get("teszt2","teszt->teszt")->view("login.php");

        /**
         * @var \Pachel\EasyFrameWork\Route $route
         */
        $route = $Routingtest->routes->find("path")->equal("teszt1")->get()[0];
        $this->assertEquals(Base::instance()->env("app.views")."layout.index.php",$route->template,"Template mentése");
        $this->assertEquals(Base::instance()->env("app.views")."layout.php",$route->layout,"Layout feldolgozása");

        $this->expectExceptionMessage(Messages::DRAW_TEMPLATE_NOT_FOUND);
        $Routingtest->get("teszt2","teszt->teszt")->view("login.phps");
    }
    /**
     * @covers Akármi
     * @return void
     */
    public function testprepare_path_to_regex(){

        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();
        $url_variables = [];
        $actual = $Routingtest->prepare_path_to_regex("*",$url_variables);
        $this->assertEquals(".*",$actual);

        $actual = $Routingtest->prepare_path_to_regex("teszt/*",$url_variables);
        $this->assertEquals('teszt\/.*',$actual);

        $actual = $Routingtest->prepare_path_to_regex("teszt/{id}/{valami}.html",$url_variables);
        $this->assertEquals('teszt\/(.+)\/(.+)\.html',$actual);

        //$this->assertEquals(["id","valami"],$url_variables,"Változók keresése");
       // $this->assertEquals([0=>'id',1=>'valami'],$url_variables,"Változók keresése");

        $actual = $Routingtest->prepare_path_to_regex("teszt/*/.html",$url_variables);
        $this->assertEquals('teszt\/\*\/\.html',$actual);




    }
    /**
     * @covers Akármi
     * @return void
     */
    public function testget_request_method(){

        $_SERVER["REQUEST_METHOD"] = "";
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");
        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();
        $request = $Routingtest->get_request_method();
        $this->assertEquals("CLI",$request);

//        $_SERVER["REQUEST_URI"] = "examples/teszt";
        $_SERVER["REQUEST_METHOD"] = "GET";
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $request = $Routingtest->get_request_method();
        $this->assertEquals("GET",$request);

        $_SERVER["REQUEST_METHOD"] = "POST";
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $request = $Routingtest->get_request_method();
        $this->assertEquals("POST",$request);


    }
    /**
     * @covers Akármi
     * @return void
     */
    public function testneg_uri(){


        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();

        $_SERVER["REQUEST_URI"] = "/easyframe/examples/teszt2";
        $_SERVER["SERVER_NAME"] = "localhost";
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["ARGV"] = [];
        $_SERVER["REQUEST_SCHEME"] = "http";
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $URI = "teszt2";
        $request = $Routingtest->neg_uri($URI);
        $this->assertEquals("teszt2",$request);

    }
    /**
     * @covers Akármi
     * @return void
     */
    public function testgenerate_uri(){


        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();

        $URI = "teszt2";
        $_SERVER["REQUEST_URI"] = "/".testConfig::dir."/examples/".$URI;
        $_SERVER["SERVER_NAME"] = "localhost";
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["ARGV"] = [];
        $_SERVER["REQUEST_SCHEME"] = "http";
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $request = $Routingtest->generate_uri();
        $this->assertEquals($URI,$request,"http kérés paramétere");

        $URI = "emailteszt";
        //$_SERVER["REQUEST_URI"] = "/easyframe/examples/".$URI;
        $_SERVER["SERVER_NAME"] = "localhost";
        $_SERVER["REQUEST_METHOD"] = "";
        $_SERVER["ARGV"] = ["index.php",$URI];
        $_SERVER["REQUEST_SCHEME"] = "";

        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $request = $Routingtest->generate_uri();
        $this->assertEquals($URI,$request,"CLI hívás paramétere");

    }
    /**
     * @covers Akármi
     * @return void
     */
    public function testget_matches_routes(){


        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();

        $URI = "teszt2";
        $_SERVER["REQUEST_URI"] = "/".testConfig::dir."/examples/".$URI;
        $_SERVER["SERVER_NAME"] = "localhost";
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["ARGV"] = [];
        $_SERVER["REQUEST_SCHEME"] = "http";
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $Routingtest->get($URI,"teszt->teszt");
        $Routingtest->get($URI."asddasdasd","teszt->teszt");
        $Routingtest->post($URI,"teszt->teszt");
        $Routingtest->post($URI."sadasd","teszt->teszt");

        $routes = $Routingtest->get_matches_routes();
        $find = $routes->find("path")->equal($URI)->get();
        $this->assertEquals(1,count($find));
        $this->assertEquals("GET",$find[0]->method);

        $_SERVER["REQUEST_METHOD"] = "POST";
        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");

        $routes = $Routingtest->get_matches_routes();
        $find = $routes->find("path")->equal($URI)->get();
        $this->assertEquals(1,count($find));
        $this->assertEquals("POST",$find[0]->method);

    }
    /**
     * @covers Akármi
     * @return void
     */
    public function testget_layout(){


        $Routingtest = new \Pachel\EasyFrameWork\Tests\RoutingTest();


        Base::instance()->config(require __DIR__."/../examples/config/dev_App.php");
        $template = Base::instance()->env("app.views")."login/layout.register.php";
        $layout =  Base::instance()->env("app.views")."layout.php";

        $active = $Routingtest->get_layout($template);
        $this->assertEquals($layout,$active,"layout a névből almappában");

        $template = Base::instance()->env("app.views")."layout.index.php";
        $active = $Routingtest->get_layout($template);
        $this->assertEquals($layout,$active,"Layout a tartalomból nem almappában");

        $template = Base::instance()->env("app.views")."login/register2.php";
        $active = $Routingtest->get_layout($template);
        $this->assertEquals($layout,$active,"Layout a tartalomból");

        $layout = Base::instance()->env("app.views")."almappa/layout2.php";
        $template = Base::instance()->env("app.views")."login/register3.php";
        $active = $Routingtest->get_layout($template);
        $this->assertEquals($layout,$active,"Layout a tartalomból, de layout egy másik mappában van");

        $layout = Base::instance()->env("app.views")."almappa/layout.php";
        $template = Base::instance()->env("app.views")."almappa/layout.register.php";
        $active = $Routingtest->get_layout($template);
        $this->assertEquals($layout,$active,"Layout a névből, azonos mappában");


        $template = Base::instance()->env("app.views")."layout.php";
        $active = $Routingtest->get_layout($template);
        $this->assertEquals("",$active,"Nincs layout");

        $template = Base::instance()->env("app.views")."almappa/layout-notexists.php";
        $this->expectExceptionCode(10100);
        $active = $Routingtest->get_layout($template);


    }
}