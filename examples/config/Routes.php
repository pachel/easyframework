<?php
namespace Pachel\EasyFrameWork;

Routing::instance()->get("*",[SmallController::class,"always"])->first();
Routing::instance()->get("/",[SmallController::class,"landing"])->view("layout.index.php");
Routing::instance()->get("dashboard/login",[SmallController::class,"dashboard3"])->view("login.php");
Routing::instance()->get("teszt",[SmallController::class,"dashboard2"])->view("layout.index.php");
Routing::instance()->get("dashboard/{category}/{id}.html",[SmallController::class,"dashboard"])->view("layout.index.php");
Routing::instance()->get("static.html")->view("login.php");
Routing::instance()->get("login")->view("login.php");
Routing::instance()->get("ss",[SmallController::class,"ss"])->view("login/layout.register.php");
/**
 * Az api kéréseknél (POST|GET) csak ez az egy metódus fut le,
 * és egy JSON objektumot ad vissza a oldal
 */
Routing::instance()->postget("api.php",[SmallController::class,"api"])->json()->onlyone();
Routing::instance()->cli("email-szinkronok",function ($app){
    /**
     * @var \Pachel\EasyFrameWork\BaseAsArgument $app
     */
    //print_r(func_get_args());

})->view("cli.php");



/**
 * Authorise
 */
Auth::instance()->policy("deny");

Auth::instance()->allow("ss/");
Auth::instance()->allow("dashboard/*");
Auth::instance()->allow("dashboard/login");
Auth::instance()->allow("api.php");
/**
 * Csak a POST|GET path-ra vonatkozik, a cli nincs ellenőrizve
 */
Auth::instance()->authorise([SmallController::class,"authorise"]);