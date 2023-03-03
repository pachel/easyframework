<?php

namespace Pachel\EasyFrameWork;

abstract class CacheBase extends Prefab
{
    private array $vars;
    private string $CACHE_DIR,$TMP_DIR;

    public int $expires = 10;
    private CacheObject $cache;
    public function __construct($CACHE_DIR)
    {
        $this->CACHE_DIR = $CACHE_DIR;
        $this->cache = new CacheObject();

    }
    private function load_cache(){
        $this->chech_dir();
        $files = scandir($this->CACHE_DIR);
        foreach ($files AS $file){
            if($file=="." || $file ==".."){
                continue;
            }

        }
    }
    private function chech_dir(){
        if(!is_dir($this->CACHE_DIR)){
            try {
                mkdir($this->CACHE_DIR,0777);
            }
            catch (\Exception $exception){
                echo $exception->getMessage();
            }
        }
    }
    private function save($name,$value){
        $find = $this->cache->search(["name"=>$name],null,true);
        $object = [
            "name" => $name,
            "content" => $value,
            "expires" => $this->expires,
            "timestamp" => time()
        ];
        //echo count($find)."\n";
        if(count($find) == 0){
            $this->cache->push($object);
        }
        else{
            $this->cache[$find[0]] = $object;
        }

    }
    private function load($name):mixed{
        $find = $this->cache->search(["name"=>$name],ListObject::SEARCH_EQUAL,true);
        if(count($find) == 0){
            return null;
        }
        else{
            return  $this->cache[$find[0]]["content"];
        }
    }
    public function hash():string{
        $ser = "";
        $arguments = func_get_args();
        foreach ($arguments AS $argument){
            $ser .= md5(serialize($argument));
        }
        return md5($ser);
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
 *
 */
class Cache extends CacheBase
{

}