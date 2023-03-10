<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

trait getMethods
{
    public function line(){
        return $this->class->get("line");
    }
    public function array():array{
        return $this->class->get("row");
    }

    /**
     * @return \stdClass|array
     */
    public function simple(){
        return $this->class->get("simple");
    }
    /*
    public function group(){
        return $this->class->get("group");
    }

    public function flat(){
        return $this->class->get("flat");
    }*/
}