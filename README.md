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
````
<?php
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

//Loads the template.html file from the folder APP.VIEWS to any get request
Routing::instance()->get("*")->view("template.html");
$App->run();
?>
````
## Config
### Required parameters
- APP
  - URL
  - UI
> If the **APP.VIEW** folder is not set up, the app is looking for templates in the **APP.UI** folder
### Nice to have
- APP
  - VIEWS
  - LOGS
### How to access configuration file data?
````
//Config array
$config = [
  "APP" => [
    "URL" => "http://localhost"  
  ]
];

//From youre code
Base::instance()->env("APP.URL");

//From html template
{{APP.URL}}

//From php template
echo $APP["URL"];
````

## Routing



**asdasd**