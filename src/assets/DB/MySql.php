<?php

namespace Pachel\EasyFrameWork\DB;

use JetBrains\PhpStorm\Deprecated;
use mysql_xdevapi\Exception;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\DB\callBacks\deletCallback;
use Pachel\EasyFrameWork\DB\callBacks\paramsCallback;
use Pachel\EasyFrameWork\DB\callBacks\queryCallback;
use Pachel\EasyFrameWork\DB\Models\Config;
use Pachel\EasyFrameWork\DB\Models\Query;
use Pachel\EasyFrameWork\DB\Traits\OldTimerMethods;
use Pachel\EasyFrameWork\Messages;


class mySql
{

    protected \PDO $PDO;
    public static $CONNECTED = false;

    private Config $CONFIG;

    protected Query $QUERY;

    public const
        RESULT_ASSOC = \PDO::FETCH_ASSOC,
        RESULT_OBJECT = \PDO::FETCH_OBJ;


    private int $result_type = self::RESULT_OBJECT;
    public const
        QUERY_TYPE_SELECT = 1,
        QUERY_TYPE_DELETE = 2,
        QUERY_TYPE_UPDATE = 3,
        QUERY_TYPE_QUERY = 4,
        QUERY_TYPE_INSERT = 5;
    private $_last_id = 0;
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
    protected function connect():void
    {
        $CONFIG = Base::instance()->env("MYSQL");
        if ($CONFIG == "" || !is_array($CONFIG)) {
            throw new \Exception(Messages::MYSQL_CONFIG_NOT_EXISTS);
        }
        $config = new Config($CONFIG);
        $this->CONFIG = $config;
        $db_dsn = 'mysql:host=' . $config->host . ';dbname=' . $config->database . ";charset=" . $config->charset;
        $this->PDO = new \PDO($db_dsn, $config->username, $config->password, null);
        $this->PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$CONNECTED = true;
    }

    /*
    public function safeMode(bool $safe){
        $this->safeMode = $safe;
    }*/
    public function setResultType(int $type): void
    {
        $this->result_type = $type;
    }

    public function query(string $sql_query): queryCallback
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_QUERY;
        $this->QUERY->sql_query = $sql_query;
        return new queryCallback($this);
    }

    protected function params(array $params): paramsCallback
    {
        //$this->QUERY->pdo_parameters = $this->arrayFromObject($params);;
        $this->QUERY->pdo_parameters = $params;
        return new paramsCallback($this);
    }

    protected function exec():bool
    {
        return $this->toDatabase($this->QUERY->sql_query, $this->QUERY->pdo_parameters);
    }




    protected function get(string $type)
    {
        switch ($type) {
            case "line":
                $type = "@line";
                break;
            case "simple":
                $type = "@simple";
                break;
            default:
                $type = "@row";
        }

        if ($this->QUERY->method == self::QUERY_TYPE_QUERY) {
            return $this->fromDatabase($this->QUERY->sql_query, $type, $this->QUERY->pdo_parameters);
        } elseif ($this->QUERY->method == self::QUERY_TYPE_SELECT) {
            $query = $this->makeQuery();
            return $this->fromDatabase($query->query, $type, $query->pdo_parameters);
        }
        return [];
    }



    public function __destruct()
    {
        //TODO: Kapcsolatot bezárni
    }

    public function __set(string $name, $value): void
    {
        if(property_exists($this,$name)){
            $this->{$name} = $value;
        }
    }
    public function __get(string $name)
    {
        if(property_exists($this,$name)){
            return $this->{$name};
        }
    }
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        } else {
            throw new \Exception(sprintf(Messages::MODEL_PROPERY_NOT_EXISTS[0], $name, $this->_classname), Messages::MODEL_PROPERY_NOT_EXISTS[1]);
        }
    }
}