<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

trait updateMethods
{
    public function where(array $where):void{
        $this->class->where($where);
    }
    public function id(int $id):void{
        $this->class->id($id);
    }
}