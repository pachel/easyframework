<?php

namespace Pachel\EasyFrameWork;

trait MethodAlias{

/*
    protected const
        METHOD_ALIASES = [
        "method" => ["get", "post", "cli", "postget"]
    ];
*/
    /**
     * @param $name
     * @param $params
     * @return int|mixed|string
     */
    protected function method_alias($name, &$params)
    {
        foreach (self::METHOD_ALIASES as $key => $alias) {
            if (in_array($name, $alias)) {
                $params = array_merge([$name], $params);
                return $key;
            }
        }
        return $name;
    }

    public function __call(string $name, array $arguments)
    {
        $name = $this->method_alias($name, $arguments);
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }

    }
}
