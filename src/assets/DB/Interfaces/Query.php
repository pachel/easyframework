<?php

namespace Pachel\EasyFrameWork\DB\Interfaces;

final class Query
{
    public int $method;
    public $select;
    public $from;
    public $where;
    public $orderby;
    public $groupby;
    public $limit;
}