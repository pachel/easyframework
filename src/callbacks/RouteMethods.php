<?php

namespace Pachel\EasyFrameWork\Traits;

use Pachel\EasyFrameWork\Callbacks\beforeMethodCallback;
use Pachel\EasyFrameWork\Callbacks\generateMethodCallback;
use Pachel\EasyFrameWork\Callbacks\RouteMethodCallback;


trait routeMethods
{
    /**
     * @param string $path
     * @param $object
     * @return RouteMethodCallback
     */
    public function get(string $path,$object = null):RouteMethodCallback
    {
        return $this->method("get",$path,$object);
    }

    /**
     * @param string $path
     * @param $object
     * @return RouteMethodCallback
     */
    public function post(string $path,$object = null):RouteMethodCallback
    {
        return $this->method("post",$path,$object);
    }

    /**
     * @param string $path
     * @param $object
     * @return RouteMethodCallback
     */
    public function postget(string $path,$object = null):RouteMethodCallback
    {
        return $this->method("postget",$path,$object);
    }

    /**
     * @param string $path
     * @param $object
     * @return RouteMethodCallback
     */
    public function cli(string $path,$object = null):RouteMethodCallback
    {
        return $this->method("cli",$path,$object);
    }
}

trait viewMethod
{
    /**
     * Itt kell beállítani a template állományt, pontosan meg kell adni
     * a template elérési útvonalát az APP.VIEWS mappán belül
     * @see https://github.com/pachel/easyframework#routing
     * @param string $template_name
     * @return generateMethodCallback
     */
    public function view(string $template_name): generateMethodCallback
    {
        return $this->class->view($template_name);
    }
}

trait firstMethod
{
    /**
     * ha több route is passzol az aktuális url-re, akkor ezze az opcióval
     * megmondhatjuk, hogy ez legyen először végrehajtva
     * @return void
     */
    public function first(): void
    {
        $this->class->first();
    }
}
trait jsonMethod
{
    /**
     * HA nem akarunk magunk json tartalmat generáli a lefuttatot methódusban
     * akkor csak egy tömböt kell visszatérési értékként használnunk és
     * ezzel az opcióval a rendszer generál belőle egy jsont, és kiírja a képernyőre
     *
     * @return void
     */
    public function json():generateMethodCallback
    {
        return $this->class->json();
    }
}
trait beforeMethod{
    /**
     * @param array|object|string $object Futtatandó script
     * @return beforeMethodCallback
     */
    public function before($object):beforeMethodCallback{
        return $this->class->before($object);
    }
}
trait onlyoneMethod{
    public function onlyone():void{

    }
}
trait allowMethod{
    /**
     * Ha nem akarjuk a Routing::instance()->allow($path) részt meghívni,
     * ezt is lehet használni annak kiváltására
     *
     * @return void
     */
    public function allow():void{
        $this->class->allow();
    }
}