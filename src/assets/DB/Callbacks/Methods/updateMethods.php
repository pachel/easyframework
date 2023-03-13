<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

trait updateMethods
{
    public function where( $where):bool{
        return $this->class->where($where);
    }
    public function id(int $id):bool{
        return $this->class->id($id);
    }
}