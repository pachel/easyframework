<?php
namespace Pachel\EasyFrameWork;

Routing::instance()->get("*",[SmallController::class,"always"])->first();

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
Routing::instance()->get("static.html")->view("login.php");
Routing::instance()->get("login")->view("login.php");
Routing::instance()->get("teszt/{id}/{valami}.html",[SmallController::class,"ss"])->view("login/layout.register.php");


Routing::instance()->get("multiples")->view("multiples/index.html");
Routing::instance()->get("withlayouts")->view("multiples/withlayout/inner.html")->allow();
Routing::instance()->get("named")->view("named/content.html")->name("content");

Routing::instance()->get("layout",function ($app){$app->kex = 4;})->view("unnamed.php")->name("content4")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 1;})->view("unnamed.php")->name("content")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 0;})->view("unnamed.php")->name("content2")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 2;})->view("unnamed.php")->name("content3")->layout("layout.php");
Routing::instance()->get("layout",function ($app){$app->kex = 3;})->view("unnamed.php")->name("js")->layout("layout.php");



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

Auth::instance()->allow("dashboard/*");
Auth::instance()->allow("login");
Auth::instance()->allow("layout");
Auth::instance()->allow("named");
Auth::instance()->allow("teszt");
Auth::instance()->allow("unnamed");
Auth::instance()->allow("dashboard/login");
Auth::instance()->allow("api.php");
/**
 * Csak a POST|GET path-ra vonatkozik, a cli nincs ellenőrizve
 */
Auth::instance()->authoriser([SmallController::class,"authoriser"]);