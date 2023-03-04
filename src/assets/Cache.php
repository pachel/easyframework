<?php

namespace Pachel\EasyFrameWork;

abstract class CacheBase extends Prefab
{
    private array $vars;
    private string $CACHE_FILE, $CACHE_DIR, $TMP_DIR;

    public int $expires = 1;
    private CacheObject $cache;

    private string $start_hash;

    private bool $loaded = false;
    public function __construct($CACHE_DIR)
    {
        $this->CACHE_DIR = $CACHE_DIR;
        $this->CACHE_FILE = $CACHE_DIR . session_id() . ".tmp";
        $this->cache = new CacheObject();
        if (is_numeric(Base::instance()->env("APP.CACHE_EXPIRES"))) {
            $this->expires = Base::instance()->env("APP.CACHE_EXPIRES");
        }


    }

    private function load_cache()
    {
        $this->loaded = true;
        $this->chech_dir();
        $files = scandir($this->CACHE_DIR);
        if (is_file($this->CACHE_FILE)) {
            $this->cache = unserialize(file_get_contents($this->CACHE_FILE));
        }
        $this->start_hash = md5(serialize($this->cache));
        $this->cache->find("timestamp")->smallerthan($this->expires * 60)->cut();

        /**
         * @var CacheObjectItem $item
         */
        /*
        $delete = [];
        foreach ($this->cache as $index => $item) {
            if ($item->timestamp < time() - ($item->expires * 60)) {
                $delete[] = $index;
            }
        }


        if (!empty($delete)) {
            $this->cache->delete($delete);
        }*/
    }

    private function save_cache()
    {
        if($this->loaded) {
            if (md5(serialize($this->cache)) != $this->start_hash) {
                @file_put_contents($this->CACHE_FILE, serialize($this->cache));
            }
        }
    }

    private function chech_dir()
    {
        if (!is_dir($this->CACHE_DIR)) {
            try {
                mkdir($this->CACHE_DIR, 0777);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }
    }

    private function save($name, $value)
    {
        if(!$this->loaded){
            $this->load_cache();
        }
        $find = $this->cache->search(["name" => $name], ListObject::SEARCH_EQUAL, true);
        $object = [
            "name" => $name,
            "content" => $value,
            "expires" => $this->expires,
            "timestamp" => time()
        ];
        //echo count($find)."\n";
        if (count($find) == 0) {
            $this->cache->push($object);
        } else {
            $this->cache[$find[0]] = $object;
        }

    }

    private function load($name): mixed
    {
        $find = $this->cache->search(["name" => $name], ListObject::SEARCH_EQUAL, true);
        if (count($find) == 0) {
            return null;
        } else {
            return $this->cache[$find[0]]["content"];
        }
    }

    public function hash(): string
    {
        $ser = "";
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            $ser .= md5(serialize($argument));
        }
        return md5($ser);
    }

    public function __set(string $name, $value): void
    {
        $this->save($name, $value);
    }

    public function __get(string $name)
    {
        return $this->load($name);
    }

    public function __destruct()
    {
        $this->save_cache();
    }
}

/**
 *
 */
class Cache extends CacheBase
{

}