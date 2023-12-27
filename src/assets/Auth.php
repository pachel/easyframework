<?php

namespace Pachel\EasyFrameWork;


use Pachel\EasyFrameWork\Callbacks\AuthPolityCallback;

class Auth extends Prefab
{
    protected static array $vars;

    protected bool $enabled = false;

    protected SiteList $allowedSitesList;

    protected SiteList $deniedSitesList;

    protected $autorise_function;
    public const
        POLICY_DENY     = "DENY",
        POLICY_ALLOW    = "ALLOW";
    protected const METHOD_ALIASES = [];
    protected string $default_policy = self::POLICY_DENY;
    use MethodAlias;
    use returnObjectArray;
    public function __construct()
    {
        $this->allowedSitesList = new SiteList();
        $this->deniedSitesList = new SiteList();
    }

    /**
     * @param string $type
     * @return void
     */
    public function policy(string $type = null)
    {
        //TODO: csak ez van egyenlőre
        if(!empty($type)) {
            $this->default_policy = self::POLICY_DENY;
        }
        return new AuthPolityCallback($this);
    }
    protected function polity_allow(){
        $this->default_policy = self::POLICY_ALLOW;
    }
    protected function polity_deny(){
        $this->default_policy = self::POLICY_DENY;
    }
    public function allow($path)
    {
        $this->allowedSitesList->push(["path" => Functions::checkSlash2($path), "path_to_regex" => Routing::instance()->prepare_path_to_regex(Functions::checkSlash2($path))]);
    }

    public function deny($path)
    {
        $this->deniedSitesList->push(["path" => Functions::checkSlash2($path), "path_to_regex" => Routing::instance()->prepare_path_to_regex(Functions::checkSlash2($path))]);
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
        $this->authorise($object);
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
         * Ha engedélyezés az alapszabály, és nincs olyan oldal amire csekkolni kellene
         * akkor engedjük a futtatást
         */
        if($this->default_policy == self::POLICY_ALLOW && $this->deniedSitesList->count() == 0){
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
        if($this->default_policy == self::POLICY_DENY) {
            /**
             * Ha van talált oldal, az jó
             * @var SiteObject $item
             */
            foreach ($this->allowedSitesList as $item) {
                if (preg_match("/^" . $item->path_to_regex . "$/", $routes->path)) {
                    return true;
                }
            }

            //Ha hitelesítés kell, és van ilyen funkció beállítva akkor azt lefuttatjuk
            return $this->run_autorise_function($routes->path);
        }
        /**
         * A az AUTH szabály Allow, akkor az aktuális URL-re le kell futtatni a
         * @var SiteObject $item
         */
        foreach ($this->deniedSitesList AS $item){
            if (preg_match("/^" . $item->path_to_regex . "$/", $routes->path)) {
                return $this->run_autorise_function($routes->path);
            }
        }
        return true;
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
            $c = Base::instance();
            $class = new $classname($c);
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