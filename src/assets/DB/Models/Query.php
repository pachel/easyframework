<?php

namespace Pachel\EasyFrameWork\DB\Models {

    final class Query
    {
        public int $method;
        public $select;
        public $from;
        public $where;

        public string $sql_query = "";

        public array $set = [];
        public array $pdo_parameters = [];
        public bool $safemode;
        public string $safefield;
    }
}