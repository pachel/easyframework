<?php
namespace Configs;
use Pachel\EasyFrameWork\Routing;
use TDF\SmallController;
class t{

}
\Pachel\EasyFrameWork\Base::instance()->set("TESZT",1);
Routing::get("/",[SmallController::class,"dashboard"]);
Routing::get("dashboard",[SmallController::class,"dashboard"]);
