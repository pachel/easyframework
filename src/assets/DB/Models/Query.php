<?php

namespace Pachel\EasyFrameWork\DB\Models {

    final class Query
    {
        public int $method;
        public $select;
        public $from;
        public $where;
        public $orderby;
        public $groupby;
        public $limit;
        public string $sql_query = "";

        public array $pdo_parameters = [];
        public bool $safemode;
    }
}