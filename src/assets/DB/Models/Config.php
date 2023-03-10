<?php

namespace Pachel\EasyFrameWork\DB\Modells;

use Pachel\EasyFrameWork\Messages;

final class Config
{
    public string $host;
    public string $database;
    public string $username;
    public string $password;
    public string $charset = "utf8";

    public bool $safemode;

    public string $safefield;

    public function __construct(array $config)
    {
        foreach ($config AS $key => $value){
            $key = strtolower($key);
            if(!property_exists($this,$key)){
                throw new \Exception(sprintf(Messages::MYSQL_PARAMETER_IS_NOT_VALID[0],$key),Messages::MYSQL_PARAMETER_IS_NOT_VALID[1]);
            }
            $this->{$key} = $value;
        }
        $vars = get_class_vars("Pachel\\EasyFrameWork\\DB\\Modells\\Config");
        foreach ($vars AS $name => $value){
            if(preg_match("/_/",$name)){
                continue;
            }
            if(empty($this->{$name})){
                throw new \Exception(sprintf(Messages::MYSQL_PARAMETER_NOT_DEFINED[0],$name),Messages::MYSQL_PARAMETER_NOT_DEFINED[1]);
            }
        }
    }
}