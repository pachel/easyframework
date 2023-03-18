<?php

namespace Pachel\EasyFrameWork\DB\callBacks\DataModel;




use Pachel\EasyFrameWork\Callbacks\CallbackBase;

final class paramsCallback extends CallbackBase
{
    /**
     *
     * @param string $where_query A feltétel ami a WHERE után jön, és tartalmazza a beadott paramétereket
     * @return makeUpdateCallback
     */
    public function update(string $where_query):makeUpdateCallback{
        return $this->class->makeUpdate($where_query);
    }

    /**
     * @param string $query A feltétel ami a WHERE után jön, és tartalmazza a beadott paramétereket
     * @return bool
     */
    public function delete(string $query):bool{
        return $this->class->makeDelete($query);
    }

    /**
     * @param string $query A feltétel ami a WHERE után jön, és tartalmazza a beadott paramétereket
     * @return makeSelectCallback
     */
    public function select(string $query):makeSelectCallback{
        return $this->class->makeSelect($query);
    }
}