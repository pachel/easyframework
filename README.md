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
### Usage
#### Example1
````
$config = [
  "APP" => [
    "URL" => "http://localhost",  
    "UI" => "UI FOLDER"  
  ]
];

$App->config($config);
````
#### Example2
config.php
````
//Content of your config file
<?php
return [
  "APP" => [
    "URL" => "http://localhost",  
    "UI" => "UI FOLDER"  
  ]
];
````
index.php
````
<?php
$App->config("Your config file's path");
````
### How to access configuration file data?
````
//From everywhere
Base::instance()->env("APP.URL");

//From text/html code
{{APP.URL}}

//From php code
echo $APP["URL"];
````

## Routing
Az útvonalakat a Routes osztály segítségével kell létrehozni, de mindenképp a **cofig beállítása** után!!
### Routes to _GET methods
````
//http://yourdomain/
$Routing = Routes::instance();
$Routing->get("/")->view("index.html");
````
### Routes to _POST methods
````
//http://yourdomain/
$Routing->post("/")->view("index.html");

````
### Routes to any methods
````
//http://yourdomain/
$Routing->postget("/")->view("index.html");
````
### Parameters of post() get() postget() cli()
#### $path


**asdasd**