<?php
namespace Pachel\EasyFrameWork;
/** @var Base $Base */
$Base->Routing()->get("*",[SmallController::class,"always"])->first();

Routing::instance()->get("regex:^api\/([a-z]{2})\/([a-z]{2})\/.*",
    function ($app,$lang,$kex){
    echo $lang."  ".$kex;
    exit();
})->allow();
Routing::instance()->get("/",function ($app){$app->reroute("teszt");})->allow();
Routing::instance()->ajax("ajax",[SmallController::class,"ajax"])->json()->allow();
Routing::instance()->get("dashboard/login",[SmallController::class,"dashboard3"])->view("login/layout.register.php");
Routing::instance()->get("teszt",[SmallController::class,"dashboard2"])->view("layout.index.php");
Routing::instance()->get("dashboard/{category}/{id}.html",[SmallController::class,"dashboard"])->view("layout.index.php")->allow();
Routing::instance()->all("static.html")->view("login.php");
Routing::instance()->get("login")->view("login.php");
Routing::instance()->get("teszt/{id}/{valami}.html",[SmallController::class,"ss"])->view("login/layout.register.php");


Routing::instance()->get("multiples")->view("multiples/index.html");
Routing::instance()->get("multiples2",[SmallController::class,"dashboard2"]);
Routing::instance()->get("withlayouts")->view("multiples/withlayout/inner.html")->allow();
Routing::instance()->get("named")->view("named/content.html")->name("content");

Routing::instance()->get("layout",function ($app){$app->kex = 4;})->view("unnamed.php")->name("content4")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 1;})->view("unnamed.php")->name("content")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 0;})->view("unnamed.php")->name("content2")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 2;})->view("unnamed.php")->name("content3")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 3;})->view("unnamed.php")->name("js")->layout("layout.php");

$Base->Routing()->get("loads")->view("loads/index.html");


/**
 * Az api kéréseknél (POST|GET) csak ez az egy metódus fut le,
 * és egy JSON objektumot ad vissza a oldal
 */

Routing::instance()->postget("api.php",[SmallController::class,"api"])->before([SmallController::class,"api_key_check"])->json()->onlyone();
Routing::instance()->cli("email-szinkronok",function ($app){
    /**
     * @var \Pachel\EasyFrameWork\BaseAsArgument $app
     */
    //print_r(func_get_args());

})->view("cli.php");



/** @var Base $Base */
$Base->Auth()->policy()->deny();

$Base->Auth()->allow("dashboard/*");
$Base->Auth()->allow("login");
$Base->Auth()->allow("loads");
$Base->Auth()->allow("layout");
$Base->Auth()->allow("named");
$Base->Auth()->allow("teszt");
$Base->Auth()->allow("unnamed");
$Base->Auth()->allow("dashboard/login");
$Base->Auth()->allow("api.php");
/**
 * Csak a POST|GET|AJAX path-ra vonatkozik, a cli nincs ellenőrizve
 */
Auth::instance()->authoriser([SmallController::class,"authoriser"]);