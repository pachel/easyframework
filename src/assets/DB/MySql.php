<?php

namespace Pachel\EasyFrameWork\DB;

use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\DB\callBacks\deletCallback;
use Pachel\EasyFrameWork\DB\callBacks\fromCallback;
use Pachel\EasyFrameWork\DB\callBacks\selectCallback;
use Pachel\EasyFrameWork\DB\Interfaces\Config;
use Pachel\EasyFrameWork\DB\Interfaces\queryMaker;
use Pachel\EasyFrameWork\DB\Interfaces\Query;
use Pachel\EasyFrameWork\DB\Traits\OldTimerMethods;
use Pachel\EasyFrameWork\Messages;
use Pachel\EasyFrameWork\MethodAlias;


class mySql
{

    protected \PDO $PDO;
    public static $CONNECTED = false;

    private Config $CONFIG;

    protected Query $QUERY;
    public const
        QUERY_TYPE_SELECT = 1,
        QUERY_TYPE_DELETE = 2,
        QUERY_TYPE_UPDATE = 3;

    use OldTimerMethods;

    public function __construct()
    {
        $this->connect();

    }


    /**
     * Set up a config from config file
     *
     * @return bool
     */
    protected function connect(): void
    {
        $CONFIG = Base::instance()->env("MYSQL");
        if ($CONFIG == "" || !is_array($CONFIG)) {
            throw new \Exception(Messages::MYSQL_CONFIG_NOT_EXISTS);
        }
        $config = new Config($CONFIG);
        $db_dsn = 'mysql:host=' . $config->host . ';dbname=' . $config->database . ";charset=" . $config->charset;
        $this->PDO = new \PDO($db_dsn, $config->username, $config->password, null);
        self::$CONNECTED = true;
    }

    /**
     * @param array|string ...$arguments
     * @return selectCallback
     */
    public function select(array|string ...$arguments): selectCallback
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_SELECT;
        $this->QUERY->select = $arguments;
        return new selectCallback($this);
    }

    protected function id(int $id)
    {
        $this->QUERY->where= [["id"=>$id]];

        if($this->QUERY->method == self::QUERY_TYPE_DELETE){
            $query = $this->makeQuery();
        }
        echo $query->query."\n";
    }

    public function delete(string $table): deletCallback
    {
        $this->QUERY = new Query();
        $this->QUERY->from = $table;
        $this->QUERY->method = self::QUERY_TYPE_DELETE;
        return new deletCallback($this);
    }

    public function fromString()
    {

    }

    public function fromName()
    {

    }

    protected function name()
    {

    }

    protected function from(...$arguments)
    {
        $this->QUERY->from = $arguments;
        return new fromCallback($this);
    }

    protected function where(...$arguments)
    {
        if($this->QUERY->method == self::QUERY_TYPE_DELETE){
            $this->makeQuery();
        }
        $this->QUERY->where = $arguments;
    }

    protected function orderby(...$arguments)
    {
        $this->QUERY->orderby = $arguments;
    }

    protected function groupby(...$arguments)
    {
        $this->QUERY->groupby = $arguments;
    }

    protected function limit(...$arguments)
    {
        $this->QUERY->limit = $arguments;
    }

    protected function get($type, $result_type = "object"): array
    {
        $query = $this->makeQuery();
        echo $query->query;
        return [];
    }

    protected function makeQuery(): queryMaker
    {
        return new queryMaker($this->QUERY);
    }

    public function __destruct()
    {
        //TODO: Kapcsolatot bezárni
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }
    }
}