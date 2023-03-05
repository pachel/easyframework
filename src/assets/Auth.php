<?php

namespace Pachel\EasyFrameWork;


class Auth extends Prefab
{
    public static array $vars;

    private bool $enabled = false;
    private string $default_policy = "allow";

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
        $this->default_policy = strtoupper($type);
    }

    public function allow($path)
    {
        $this->allowedSitesList->push(["path" => $path]);
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
        if(!$this->enabled){
            return true;
        }
        $method = $this->get_object($this->autorise_function);
        if (empty($method)){
            return true;
        }
        if($routes[0]->method == "CLI"){
            return true;
        }

        if(count($routes)>1){
            //TODO: Üzi kell
            throw new \Exception("");
        }


        return true;
    }

}

final class SiteList extends ListObject
{
    protected $class = SiteObject::class;
}

/**
 * @property string path
 */
final class SiteObject extends ListObject
{
}