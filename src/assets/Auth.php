<?php

namespace Pachel\EasyFrameWork;


class Auth extends Prefab
{
    public static array $vars;

    protected bool $enabled = false;
    protected string $default_policy = "deny";

    protected SiteList $allowedSitesList;

    protected $autorise_function;
    protected const
        METHOD_ALIASES = [];

    use MethodAlias;
    use returnObjectArray;
    public function __construct()
    {
        $this->allowedSitesList = new SiteList();
    }

    /**
     * @param string $type
     * @return void
     */
    public function policy(string $type)
    {
        //TODO: csak ez van egyenlőre
        $type = "deny";
        $this->default_policy = strtoupper($type);
    }

    public function allow($path)
    {
        $this->allowedSitesList->push(["path" => Functions::checkSlash2($path), "path_to_regex" => Routing::instance()->prepare_path_to_regex(Functions::checkSlash2($path))]);
    }

    public function deny()
    {

    }

    /**
     * @param mixed $object
     * @return void
     * @deprecated
     */
    public function authorise($object)
    {
        $this->enabled = true;
        $this->autorise_function = $object;
    }
    /**
     * @param mixed $object
     * @return void
     */
    public function authoriser($object)
    {
        $this->enabled = true;
        $this->autorise_function = $object;
    }



    /**
     * @param Route $routes
     * @return bool
     */
    protected function is_authorised($routes): bool
    {

        if (!$this->enabled) {
            return true;
        }
        if($routes->path == "*"){
            return true;
        }
        if($routes->allow){
            return true;
        }
        /**
         * Ha nincs beállítva hitelesítő metódus, akkor nem kell hitelesíteni
         */
        $method = $this->get_object($this->autorise_function);
        if (empty($method)) {
            return true;
        }

        if (empty($routes)) {
            return true;
        }
        /**
         * A CLI kéréseket nem kell autorizálni
         */
        if ($routes->method == "CLI") {
            return true;
        }
        /**
         * Ha több template van betöltve, akkor dobunk egy hibát
         */
        /*if (count($routes) > 1) {
            //TODO: Üzi kell
           // throw new \Exception("",10102);
        }*/

        /**
         * Ha van talált oldal, az jó
         * @var SiteObject $item
         */



        foreach ($this->allowedSitesList as $item) {
            if (preg_match("/^" . $item->path_to_regex . "$/", $routes->path)) {
                return true;
            }
        }

        //TODO: AUTH FÜGGVÉNYT LE KELL FUTTATNI
        return $this->run_autorise_function($routes->path);

    }

    protected function run_autorise_function($path)
    {
        $object = Base::instance()->get_object($this->autorise_function);
        if(empty($object)){
            return false;
        }

        /**
         * HA osztályt hívunk meg
         */
        if (!empty($object->className)){
            $classname = $object->className;
            $class = new $classname(Base::instance());
            return $class->{$object->methodName}($path);
        }
        /**
         * Névtelen függvény hívása
         */
        elseif (!empty($object->object)){
            $arguments = [Base::instance(),$path];
            return call_user_func_array($object->object,$arguments);
        }
        return false;
    }
}

final class SiteList extends ListObject
{
    protected $class = SiteObject::class;
}

/**
 * @property string path
 * @property string path_to_regex
 */
final class SiteObject extends ListObjectItem
{
}