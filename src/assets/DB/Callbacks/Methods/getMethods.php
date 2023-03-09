<?php

namespace Pachel\EasyFrameWork\DB\callBacks\Methods;

trait getMethods
{
    public function object():array{
        return $this->class->get("object");
    }
    public function array():array{
        return $this->class->get("array");
    }
    public function simple(string $type="object"):\stdClass|array{
        return $this->class->get("simple",$type);
    }
}