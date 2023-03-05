<?php

namespace Pachel\EasyFrameWork;

/**
 * @author pachel82@gmail.com
 * @method Route current()
 * @method Route offsetGet(mixed $offset)
 * @method Route[] search(array|string $search)
 * @method Route[] match(array|string $search)
 */
class Routes extends ListObject
{
    protected $class = Route::class;


    public function matchesroutes():Routes
    {
        //print_r($this->containter[0]["template"]);
        $URI = Routing::instance()->generate_uri();
        $METHOD = Routing::instance()->get_request_method();
        $routes = new Routes();
        foreach ($this->containter AS $item){
            if(preg_match("/^".$item->path_to_regex."$/",$URI,$preg) && preg_match("/".$METHOD."/i",$item->method)){
                if(is_array($item->url_variables) && count($item->url_variables) == count($preg)-1){
                    $a = [];
                    $x=1;
                    foreach ($item->url_variables AS $name){
                        $a[]=$preg[$x];
                        $x++;
                    }
                    $item->url_variables = $a;
                }
                $routes->push($item);
            }
        }
        return $routes;
    }
}

/**
 * @author pachel82@gmail.com
 * @property string first;
 * @property string path;
 * @property string path_to_regex;
 * @property string[] url_variables;
 * @property string template;
 * @property string layout;
 * @property string method;
 * @property mixed object;
 * @property mixed return;
 * @property bool onlyone;
 * @property string direct;
 */
final class Route extends ListObjectItem
{
}
