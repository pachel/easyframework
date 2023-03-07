<?php
namespace Pachel\EasyFrameWork\Tests;



trait GetSetProperties{

    public function __get(string $name)
    {
        if(property_exists($this,$name)){
            return $this->{$name};
        }
    }
    public function __set(string $name, $value): void
    {
        if(property_exists($this,$name)){
            $this->{$name} = $value;
        }
    }
}