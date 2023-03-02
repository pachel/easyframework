<?php

namespace Pachel\EasyFrameWork;

abstract class CacheBase extends Prefab
{
    private array $vars;
    public function __construct()
    {

    }
    private function save($name,$value){
        $this->vars[$name] = $value;
    }
    private function load($name){
        if(!isset($this->vars[$name])){
            return null;
        }
        return $this->vars[$name];
    }
    public function __set(string $name, $value): void
    {
        $this->save($name,$value);
    }
    public function __get(string $name)
    {
        return $this->load($name);
    }
}

/**
 * @method
 */
class Cache extends CacheBase
{

}