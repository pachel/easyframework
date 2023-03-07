# Easy Framework
> klasj dlkasjdlkjasdél kj
>
> asjdh kjash dkjash jkdh ashd
> 
> jash dkjash djk
> 


- asdasdasdas
- asdasdasd
  - asdasdasd

## Basic usage
___
````
<?php
namespace Pachel\EasyFrameWork;
session_start();
ob_start();
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\Routing;

require_once __DIR__."/../vendor/autoload.php";

$config = [
  "APP" => [
      "URL" => "http://localhost/easyframe/examples/",
      "UI" => __DIR__."/../UI/",
      "VIEWs" => __DIR__."/../UI/views/",
      "LOGS" => __DIR__."/../logs/"            
  ]
];
$App = Base::instance();
$App->config($config);

//Loads the template.php file from the folder APP.VIEWS to any get request
Routing::instance()->get("*")->view("template.php");
$App->run();
?>
````




**asdasd**