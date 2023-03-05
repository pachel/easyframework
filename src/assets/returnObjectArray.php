<?php

namespace Pachel\EasyFrameWork;
trait returnObjectArray
{
    protected function get_object($object):objectProperty
    {
        $return = new objectProperty();
        if (is_object($object)) {
            $return->object = $object;

        } elseif (is_array($object) && method_exists($object[0], $object[1])) {
            $return->className = $object[0];
            $return->methodName = $object[1];

        } else {
            if(preg_match("/(.+)\->(.+)/",$object,$preg)){
                $return->className = $preg[1];
                $return->methodName = $preg[2];
            }
        }
        return $return;
    }
}

final class objectProperty
{
    public string $className;
    public string $methodName;

    public object $object;
}
