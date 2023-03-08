<?php

namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\Callbacks\generateMethodCallback;
use Pachel\EasyFrameWork\Callbacks\RouteMethodCallback;
use Pachel\EasyFrameWork\Callbacks\beforeMethodCallback;

/**
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback get(string $path, array|string|object $object);
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback post(string $path, array|string|object $object);
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback cli(string $args, array|string|object $object);
 * @method  \Pachel\EasyFrameWork\Callbacks\RouteMethodCallback postget(string $path, array|string|object $object);
 */
class Routing extends Prefab
{
    public Routes $routes;
    protected string $to_regex_replace;

    protected const
        METHOD_ALIASES = [
        "method" => ["get", "post", "cli", "postget"],
        "generate" => ["json"]
    ];

    use MethodAlias;

    protected $vars;

    public function __construct()
    {
        $this->routes = new Routes();
        $this->to_regex_replace = $this->load_to_regex_replace();
    }

    protected function first()
    {
        /*
        foreach ($this->routes AS $index => $route){
            $this->routes[$index]->first = 0;
        }
        $this->routes[$this->routes->count()-1]->first = 1;
        */
        $count = $this->routes->count() - 1;
        $route = $this->routes[$count];
        $this->routes->delete($count);
        $this->routes->pop($route);


    }

    /**
     * @param array|string|object $object
     * @return beforeMethodCallback
     */
    protected function before($object): beforeMethodCallback
    {
        $this->routes[$this->routes->count() - 1]->before = $object;
        return new beforeMethodCallback($this);
    }

    protected function onlyone()
    {
        $this->routes[$this->routes->count() - 1]->onlyone = true;
    }

    protected function method($type, $path, $object = null)
    {
        $route = new Route();
        $route->path = Functions::checkSlash2($path);
        $route->path_to_regex = $this->prepare_path_to_regex($route->path, $variables);
        $route->url_variables = $variables;
        $route->method = strtoupper($type);
        $route->object = $object;
        $this->routes->push($route);

        return new RouteMethodCallback($this);
    }

    /**
     * @return void
     * @throws \Exception
     * @example get()->view("layout.php");
     */
    protected function view($template)
    {
        $this->is_efw_configured();
        //print_r($template);
        $templatefile = Base::instance()->env("APP.VIEWS") . $template;
        if (!file_exists($templatefile)) {
            throw new \Exception(Messages::DRAW_TEMPLATE_NOT_FOUND);
        }
        $this->routes[$this->routes->count() - 1]->template = $templatefile;
        //HA view, csak akkor kell a layout paraméter
        $this->routes[$this->routes->count() - 1]->layout = $this->get_layout($templatefile);
        return new generateMethodCallback($this);
    }

    protected function generate($type)
    {
        $this->routes[$this->routes->count() - 1]->direct = $type;
        return new generateMethodCallback($this);
    }

    protected function is_efw_configured()
    {
        if (!Base::instance()->env("EFW.CONFIGURED")) {
            throw new \Exception(Messages::BASE_APP_NOT_CONFIGURED);
        }
    }

    protected function get_data_from_arguments($args)
    {
        $route = new Route([
            "path" => ($args[1] == "*" ? "*" : Functions::checkSlash2($args[1])),
            "method" => strtoupper($args[0]),
            "object" => $args[2]
        ]);
        //HA view, csak akkor kell a layout paraméter
        if (isset($args[3])) {
            $templatefile = Base::instance()->env("APP.VIEWS") . $args[3];
            if (!file_exists($templatefile)) {
                throw new \Exception(Messages::DRAW_TEMPLATE_NOT_FOUND);
            }
            $route->template = $templatefile;
            //HA view, csak akkor kell a layout paraméter
            $route->layout = $this->get_layout($templatefile);
        }
        $route->path_to_regex = $this->prepare_path_to_regex($route->path, $variables);
        $route->url_variables = $variables;
        return $route;
    }
    private function load_to_regex_replace(){
        $to_replace = file_get_contents(__DIR__."/../helpers/to-regex-replace.txt");
        $s = "";
        for($x=0;$x<mb_strlen($to_replace);$x++){
            $char = mb_substr($to_replace,$x,1);
            $s.='\\'.$char;

        }
        return $s;
    }
    public function prepare_path_to_regex($path, &$url_variables = null): string
    {
        if ($path == "*") {
            return ".*";
        }
        $to_replace = $this->to_regex_replace;

        $path_to_regex = preg_replace("/\*$/", "%", $path);

        if (preg_match_all("/\{(.+?)\}/", $path, $preg)) {
            $url_variables = $preg[1];
            $path_to_regex = str_replace($preg[0], "##", ($path_to_regex != "" ? $path_to_regex : $path));
//            $path_to_regex = preg_replace("/([\/\-\{\}\[\]\.\+\*\?\$\^\(\)\\\\|])/", "\\\\$1", $path_to_regex);
            $path_to_regex = preg_replace("/([".$to_replace."])/", "\\\\$1", $path_to_regex);
        } else {
//            $path_to_regex = preg_replace("/([\/\-\{\}\[\]\.\+\*\?\$\^\(\)\\\\|])/", "\\\\$1", ($path_to_regex != "" ? $path_to_regex : $path));
            $path_to_regex = preg_replace("/([".$to_replace."])/", "\\\\$1", ($path_to_regex != "" ? $path_to_regex : $path));
        }

        //Minden regexes kifejezést ki kell iktatni a kereséshez


        $path_to_regex = str_replace(["##", "%"], ["(.+)", ".*"], $path_to_regex);
//        echo $route->path_to_regex."\n";
        return $path_to_regex;
    }

    protected function get_layout($template): string
    {
        $content = file_get_contents($template);
        if (preg_match("/<!\-\-layout:(.+)\-\->/i", $content, $preg)) {
            if (!is_file(Base::instance()->env("APP.VIEWS") . $preg[1])) {
                throw new \Exception("Layout not exists: " . Base::instance()->env("APP.VIEWS") . $preg[1],10100);
            }
            return Base::instance()->env("APP.VIEWS") . $preg[1];
        }
        elseif (preg_match("/<!\-\-.*\[layout:([a-z0-9_\.\/]+)\].*\-\->/i",$content,$preg)){
            if (!is_file(Base::instance()->env("APP.VIEWS") . $preg[1])) {
                throw new \Exception("Layout not exists: " . Base::instance()->env("APP.VIEWS") . $preg[1],10100);
            }
            return Base::instance()->env("APP.VIEWS") . $preg[1];
        }
        else {
            if(!preg_match("/^".$this->prepare_path_to_regex(Base::instance()->env("APP.VIEWS"))."(.*)\/(.+?)\.(.+?)\.(.*)$/",$template,$preg)){
                return "";
            }

            return $this->search_layout_from_name($preg[2],$preg[1],$preg[4]);

        }
        return "";
    }

    protected function search_layout_from_name($layout,$dir,$ext): string
    {
        $view = Base::instance()->env("APP.VIEWS");
        $files = scandir($view.$dir);

        foreach ($files AS $file){
            if($file == "." || $files == ".."){
                continue;
            }
            if(preg_match("/^".$this->prepare_path_to_regex($layout)."\.".$ext."$/",$file)){
                return Functions::checkSlash($view.$dir).$file;
            }
        }
        if($dir!=""){
            $next = Functions::detract_last_dir($dir);
            return $this->search_layout_from_name($layout,$next,$ext);
        }

        return "";
    }

    public function get_request_method()
    {
        $method = Base::instance()->env("SERVER.REQUEST_METHOD");
        if (empty($method)) {
            return "CLI";
        }
        return $method;
    }

    protected function neg_uri($URI)
    {
        $URI = preg_replace("/\?.*$/", "", $URI);
        $full = Base::instance()->env("SERVER.REQUEST_SCHEME") . "://" . Base::instance()->env("SERVER.server_name") . $URI;
        $d = explode(Base::instance()->env("APP.URL"), $full);
        if (count($d) == 2) {
            $URI = Functions::checkSlash2($d[1]);
        } else {
            //  $URI = "";
        }
        return $URI;
    }

    public function get_matches_routes(): Routes
    {
        //ob_clean();
        // echo Base::instance()->env("SERVER.REQUEST_URI");

        return $this->routes->matchesroutes();
    }

    public function generate_uri(): string
    {
        if (!isset(self::$VARS["ROUTE"]) || empty(self::$VARS["ROUTE"])) {

            $URI = Base::instance()->env("SERVER.REQUEST_URI");
            $argv = Base::instance()->env("SERVER.argv");
            if (!empty($argv)) {
                if (count($argv) == 1) {
                    $URI = "";
                } else {
                    $URI = $argv[1];
                }
            } else {
                $URI = $this->neg_uri($URI);
            }
            $this->vars["uri"] = $URI;
            return $URI;
        } else {
            return $this->vars["uri"];
        }
    }
}