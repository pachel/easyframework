<?php

namespace Pachel\EasyFrameWork;

use JetBrains\PhpStorm\Deprecated;
use Pachel\EasyFrameWork\Callbacks\generateMethodCallback;
use Pachel\EasyFrameWork\Callbacks\layoutMethodCallback;
use Pachel\EasyFrameWork\Callbacks\RouteMethodCallback;
use Pachel\EasyFrameWork\Callbacks\beforeMethodCallback;
use Pachel\EasyFrameWork\Callbacks\nameMethodCallback;
use Pachel\EasyFrameWork\Traits\getMethod;
use Pachel\EasyFrameWork\Traits\routeMethods;


class Routing extends Prefab
{
    protected Routes $routes;
    protected string $to_regex_replace;

    protected const
        METHOD_ALIASES = [
        "method" => ["get", "post", "cli", "postget","ajax"],
        "generate" => ["json"]
    ];

    use MethodAlias;

    /**
     * Callback függvények behívása
     */
    use routeMethods;

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

    protected function allow()
    {
        $this->routes[$this->routes->count() - 1]->allow = true;
    }

    protected function method($type, $path, $object = null)
    {
        $route = new Route();
        $route->path = Functions::checkSlash2($path);
        //$route->path_original = $path;
        $route->path_to_regex = $this->prepare_path_to_regex($path, $variables);
        $route->url_variables = $variables;
        $route->method = strtoupper($type);
        $route->object = $object;
        $route->index = $this->routes->count();
        $this->routes->push($route);

        return new RouteMethodCallback($this);
    }

    /**
     * @return void
     * @throws \Exception
     * @example get()->view("layout.php");
     */
    protected function view($template): generateMethodCallback
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

    /**
     * Meg lehet nevezni a templatet, így nem kell a kódva ágyazni a nevet,
     * ha egy layoutba lesz meghívva, viszont a layoutban erre a névre kell majd hivatkozni!
     *
     * @param string $template_name
     * @return nameMethodCallback
     */
    protected function name(string $template_name): nameMethodCallback
    {
        $this->routes[$this->routes->count() - 1]->name = $template_name;
        return new nameMethodCallback($this);
    }

    protected function layout(string $layout): layoutMethodCallback
    {
        $layout = Base::instance()->env("app.views") . $layout;
        if (!file_exists($layout)) {
            throw new \Exception(Messages::DRAW_TEMPLATE_NOT_FOUND);
        }
        $this->routes[$this->routes->count() - 1]->layout = $layout;
        return new layoutMethodCallback($this);
    }

    /**
     * Direkt tipus beállítva, ez egyenlőre csak json lehet, de majd később lehet, hogy bővülni fog
     *
     * @param $type
     * @return generateMethodCallback
     */
    protected function generate($type): generateMethodCallback
    {
        $this->routes[$this->routes->count() - 1]->direct = $type;
        return new generateMethodCallback($this);
    }

    /**
     * Csak annyit csinál a függvény, hogy megnézi azt, hogy a config állomány be lett-e már töltve
     *
     * @return void
     * @throws \Exception
     */
    protected function is_efw_configured()
    {
        if (!Base::instance()->env("EFW.CONFIGURED")) {
            throw new \Exception(Messages::BASE_APP_NOT_CONFIGURED);
        }
    }

    #[Deprecated]
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

    /**
     * A to-regex-replace.txt fájlban vannak azok a karakterek, amiket ki kell
     * escapelni a szövegből hogy, működjön a regex
     *
     * @return string
     */
    protected function load_to_regex_replace()
    {
        $to_replace = file_get_contents(__DIR__ . "/../helpers/to-regex-replace.txt");
        $s = "";
        for ($x = 0; $x < mb_strlen($to_replace); $x++) {
            $char = mb_substr($to_replace, $x, 1);
            $s .= '\\' . $char;

        }
        return $s;
    }

    /**
     * Ez készíti elő a path értékét úgy, hogy kicseréli a karaktereket a megfelelő módon
     * pl. a * mint helyettesítő karakter csak a path végén működik, ha csak csillag
     * van a path-ben akkor az .* lesz, tehát minden GET|POST|CLI kérésre lefut
     * A változókat is itt szedi ki, ha pl. {valami} van a path, ben akkor azt
     * keresni fogja később a rendszer és átadja paraméterben a methódusnak ami
     * a path-hoz lett beállíva
     *
     * @param string $path
     * @param array $url_variables
     * @return string
     */
    protected function prepare_path_to_regex($path, &$url_variables = null): string
    {
        if ($path == "*") {
            return ".*";
        }

        if(preg_match("/^regex:(.+)$/",$path,$preg)){
            preg_match_all("/\([^\)]+\)/",$preg[1],$preg2);
            if(count($preg2[0])>0){
                $c = 0;
                foreach ($preg2[0] AS $item){
                    $url_variables[] = "item".$c;
                    $c++;
                }

            }

            return $preg[1];
        }
        $path = preg_replace("/\/$/","",$path);

        $to_replace = $this->to_regex_replace;
        /*
        $to_replace2 = '';
        for ($x=0;$x<strlen($to_replace);$x++){
            $to_replace2.=preg_quote(mb_substr($to_replace,$x,1));
        }*/
        $path_to_regex = preg_replace("/\*$/", "%", $path);

        $counter = 0;
        if (preg_match_all("/\{(.+?)\}(\([0-9]+\))?/", $path, $preg)) {
            //print_r($preg);
            $url_variables = $preg[1];

            foreach ($preg[1] as $index => $ct) {
                $path_to_regex = str_replace($preg[0][$index], "##".(!empty($preg[2][$index])?"______".str_replace(["(",")"],"",$preg[2][$index]):""), ($path_to_regex != "" ? $path_to_regex : $path));
               // $path_to_regex = preg_replace("/([\/\-\{\}\[\]\.\+\*\?\$\^\(\)\\\\|])/", "\\\\$1", $path_to_regex);
         //       $to_replace = "\/\.\-\?\+\[\]\{\}\(\)\*\|";
              //  echo $to_replace2."\n";


            }
            //echo $path_to_regex." : 2\n";
            $path_to_regex = preg_replace("/([".$to_replace."])/i", "\\\\$1", $path_to_regex);
            /*
            /*
            if ($counter > 0) {
                $to = "!!" . str_replace(["(", ")"], "", $counter);
            }*/


        } else {
//            $path_to_regex = preg_replace("/([\/\-\{\}\[\]\.\+\*\?\$\^\(\)\\\\|])/", "\\\\$1", ($path_to_regex != "" ? $path_to_regex : $path));
            $path_to_regex = preg_replace("/([" . $to_replace . "])/", '\\\\$1', ($path_to_regex != "" ? $path_to_regex : $path));
        }

        //Minden regexes kifejezést ki kell iktatni a kereséshez

        $path_to_regex = preg_replace("/(##)______([0-9]+)/",'(.{$2})', $path_to_regex);
        $path_to_regex = str_replace(["##", "%"], ["(.+)", ".*"], $path_to_regex);
        /*
        if($path == "dashboard/{category}/{id}.html") {
            echo $path_to_regex . " : 1\n";
          //  echo $path . "\n";
            print_r($preg);
            exit();
        }*/
        //echo $path_to_regex."\n";
        //exit();
        return $path_to_regex;
    }

    /**
     * Ha van a template-ben hivatkozás a layoutra, akkor annak helyét adja vissza
     *
     * @param string $template
     * @return string
     * @throws \Exception
     */
    protected function get_layout(string $template): string
    {
        $content = file_get_contents($template);
        if (preg_match("/<!\-\-layout:(.+)\-\->/i", $content, $preg)) {
            if (!is_file(Base::instance()->env("APP.VIEWS") . $preg[1])) {
                throw new \Exception("Layout not exists: " . Base::instance()->env("APP.VIEWS") . $preg[1], 10100);
            }
            return Base::instance()->env("APP.VIEWS") . $preg[1];
        } elseif (preg_match("/<!\-\-.*\[layout:([a-z0-9_\.\/]+)\].*\-\->/i", $content, $preg)) {
            if (!is_file(Base::instance()->env("APP.VIEWS") . $preg[1])) {
                throw new \Exception("Layout not exists: " . Base::instance()->env("APP.VIEWS") . $preg[1], 10100);
            }
            return Base::instance()->env("APP.VIEWS") . $preg[1];
        } else {
            if (!preg_match("/^" . $this->prepare_path_to_regex(Base::instance()->env("APP.VIEWS")) . "(.*)\/([^\.]+)\.([^\.]+)\.([^\.]+)$/", $template, $preg)) {





                return "";
            }

            return $this->search_layout_from_name($preg[2], $preg[1], $preg[4]);

        }
        return "";
    }

    /**
     * Ha a név valami.ize.php akkor a valami.php-t keresi a vele egy szinten lévő
     * mappában, vagy visszább, egészen az APP.VIEW mappa gyökeréig
     *
     * @param string $layout
     * @param string $dir
     * @param string $ext
     * @return string
     */
    protected function search_layout_from_name(string $layout, string $dir, string $ext): string
    {
        $view = Base::instance()->env("APP.VIEWS");
        $files = scandir($view . $dir);

        foreach ($files as $file) {
            if ($file == "." || $files == "..") {
                continue;
            }
            if (preg_match("/^" . $this->prepare_path_to_regex($layout) . "\." . $ext . "$/", $file)) {
                return str_replace("//","/",Functions::checkSlash($view . $dir)) . $file;
            }
        }
        if ($dir != "") {
            $next = Functions::detract_last_dir($dir);
            return $this->search_layout_from_name($layout, $next, $ext);
        }

        return "";
    }

    /**
     * Visszadja, hogy milyen request methoddal hívtuk meg az appunkat
     * ez most GET|POST|CLI lehet
     *
     * @return array|mixed|string
     */
    protected function get_request_method()
    {
        $method = Base::instance()->env("SERVER.REQUEST_METHOD");
        if (empty($method)) {
            return "CLI";
        }
        if(Base::instance()->env("SERVER.HTTP_X_REQUESTED_WITH") == "XMLHttpRequest"){
            $method = "AJAX";
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

    protected function get_matches_routes(): Routes
    {
        //ob_clean();
        // echo Base::instance()->env("SERVER.REQUEST_URI");

        return $this->routes->matchesroutes();
    }

    protected function generate_uri(): string
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