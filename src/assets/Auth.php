<?php

namespace Pachel\EasyFrameWork;


class Auth extends Prefab
{
    public static array $vars;

    private bool $enabled = false;
    private string $default_policy = "deny";

    private SiteList $allowedSitesList;

    private $autorise_function;
    protected const
        METHOD_ALIASES = [];

    use MethodAlias;

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
        $this->allowedSitesList->push(["path" => $path, "path_to_regex" => Routing::instance()->prepare_path_to_regex($path)]);
    }

    public function deny()
    {

    }

    /**
     * @param mixed $object
     * @return void
     */
    public function authorise($object)
    {
        $this->enabled = true;
        $this->autorise_function = $object;
    }

    use returnObjectArray;

    /**
     * @param Route[] $routes
     * @return bool
     */
    private function is_authorised($routes): bool
    {

        if (!$this->enabled) {
            return true;
        }
        /**
         * Ha nincs beállítva hitelesítő metódus, akkor nem kell hitelesíteni
         */
        $method = $this->get_object($this->autorise_function);
        if (empty($method)) {
            return true;
        }

        if (count($routes) == 0) {
            return true;
        }
        /**
         * A CLI kéréseket nem kell autorizálni
         */
        if ($routes[0]->method == "CLI") {
            return true;
        }
        /**
         * Ha több template van betöltve, akkor dobumnt egy hibát
         */
        if (count($routes) > 1) {
            //TODO: Üzi kell
            throw new \Exception("");
        }

        /**
         * Ha van talált oldal, az jó
         * @var SiteObject $item
         */
        foreach ($this->allowedSitesList as $item) {
            if (preg_match("/^" . $item->path_to_regex . "$/", $routes[0]->path)) {
                return true;
            }
        }

        //TODO: AUTH FÜGGVÉNYT LE KELL FUTTATNI
        return $this->run_autorise_function($routes[0]->path);

    }

    private function run_autorise_function($path)
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