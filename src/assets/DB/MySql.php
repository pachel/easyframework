<?php

namespace Pachel\EasyFrameWork\DB;

use mysql_xdevapi\Exception;
use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\DB\callBacks\deletCallback;
use Pachel\EasyFrameWork\DB\callBacks\deleteCallback;
use Pachel\EasyFrameWork\DB\callBacks\fromCallback;
use Pachel\EasyFrameWork\DB\callBacks\idCallback;
use Pachel\EasyFrameWork\DB\callBacks\insertCallback;
use Pachel\EasyFrameWork\DB\callBacks\paramsCallback;
use Pachel\EasyFrameWork\DB\callBacks\queryCallback;
use Pachel\EasyFrameWork\DB\callBacks\selectCallback;
use Pachel\EasyFrameWork\DB\callBacks\setCallback;
use Pachel\EasyFrameWork\DB\callBacks\updateCallback;
use Pachel\EasyFrameWork\DB\callBacks\whereCallback;
use Pachel\EasyFrameWork\DB\Modells\Config;
use Pachel\EasyFrameWork\DB\Modells\queryMaker;
use Pachel\EasyFrameWork\DB\Modells\Query;
use Pachel\EasyFrameWork\DB\Traits\OldTimerMethods;
use Pachel\EasyFrameWork\Functions;
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
        QUERY_TYPE_UPDATE = 3,
        QUERY_TYPE_QUERY = 4,
        QUERY_TYPE_INSERT = 5;
    public const
        RESULT_ASSOC = \PDO::FETCH_ASSOC,
        RESULT_OBJECT = \PDO::FETCH_OBJ;


    private int $result_type = self::RESULT_OBJECT;

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
        $this->CONFIG = $config;
        $db_dsn = 'mysql:host=' . $config->host . ';dbname=' . $config->database . ";charset=" . $config->charset;
        $this->PDO = new \PDO($db_dsn, $config->username, $config->password, null);
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

    /**
     * @param array|string ...$arguments
     * @return selectCallback
     */

    private function select(object $object): selectCallback
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_SELECT;
        $this->QUERY->select = $this->arrayFromObject($object);
        //$this->QUERY->select = "*";

        return new selectCallback($this);
    }

    public function query(string $sql_query): queryCallback
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_QUERY;
        $this->QUERY->sql_query = $sql_query;
        return new queryCallback($this);
    }

    public function params(array $params): paramsCallback
    {
        $this->QUERY->pdo_parameters = $this->arrayFromObject($params);;
        return new paramsCallback($this);
    }

    protected function exec()
    {
        $this->toDatabase($this->QUERY->sql_query, $this->QUERY->pdo_parameters);
    }

    protected function set($data)
    {
        if ($this->QUERY->method == self::QUERY_TYPE_INSERT) {
            $this->insert2($this->QUERY->from, $this->arrayFromObject($data));
            return;
        }
        $this->QUERY->pdo_parameters = $this->arrayFromObject($data);
        return new setCallback($this);
    }

    private function arrayFromObject($data): array
    {
        if (!is_object($data)) {
            return $data;
        }
        if (method_exists($data, "tableName")) {
            $this->QUERY->from = $data->tableName();
            $primary = $data->primaryName();
            if (isset($data->{$primary}) && $this->QUERY->method == self::QUERY_TYPE_UPDATE) {
                $this->QUERY->where = [$primary => $data->{$primary}];
                unset($data->{$primary});
            } elseif (!isset($data->{$primary}) && $this->QUERY->method == self::QUERY_TYPE_UPDATE) {
                throw new \Exception(Messages::MYSQL_OBJECT_UPDATE_NOT_ALLOWED[0], Messages::MYSQL_OBJECT_UPDATE_NOT_ALLOWED[1]);
            } elseif (!isset($data->{$primary}) && $this->QUERY->method == self::QUERY_TYPE_DELETE && !$this->QUERY->safemode) {
                throw new \Exception(Messages::MYSQL_OBJECT_DELETE_NOT_ALLOWED[0], Messages::MYSQL_OBJECT_DELETE_NOT_ALLOWED[1]);
            } elseif ($this->QUERY->method == self::QUERY_TYPE_DELETE) {
                foreach ($data as $key => $value) {
                    $this->QUERY->where[$key] = $value;
                }
            }
        }
        foreach ($data as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

    protected function id(int $id)
    {
        $this->QUERY->where = ["id" => $id];

        if ($this->QUERY->method == self::QUERY_TYPE_DELETE) {
            if ($this->QUERY->safemode) {
                $this->update2($this->QUERY->from, [$this->CONFIG->safefield => 1], ["id" => $id]);
                return;
            } else {
                //TODO:
                $query = $this->makeQuery();

            }
        } elseif ($this->QUERY->method == self::QUERY_TYPE_UPDATE) {
            // $query = $this->makeQuery();
            $this->update2($this->QUERY->from, $this->QUERY->pdo_parameters, $this->QUERY->where);
        } elseif ($this->QUERY->method == self::QUERY_TYPE_SELECT) {
            return new idCallback($this);
        }

        //echo $query->query . "\n";
    }

    public function insert($table)
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_INSERT;
        if (is_object($table)) {
            $data = $this->arrayFromObject($table);
            if (!empty($data)) {
                $this->insert2($this->QUERY->from, $data);
            }
            return;
        } elseif (!is_string($table)) {
            throw new \Exception(Messages::PARAMETER_TYPE_ERROR);
        }
        $this->QUERY->from = $table;
        return new insertCallback($this);
    }

    public function delete($table, $safe = null)
    {

        if (!is_bool($safe)) {
            $safe = $this->CONFIG->safemode;
        }
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_DELETE;
        $this->QUERY->safemode = $safe;

        if (is_object($table)) {
            $this->arrayFromObject($table);
            if (!empty($this->QUERY->where)) {
                if ($safe) {
                    $this->update2($this->QUERY->from, [$this->CONFIG->safefield => 1], $this->QUERY->where);
                } else {
                    $query = $this->makeQuery();
                    $this->query($query->query)->params($query->pdo_parameters)->exec();
                }
            }
            return;
        } elseif (!is_string($table)) {
            throw new \Exception(Messages::PARAMETER_TYPE_ERROR);
        }

        $this->QUERY->from = $table;

        return new deleteCallback($this);
    }


    public function update($table)
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_UPDATE;
        if (is_object($table)) {
            $data = $this->arrayFromObject($table);
            if (!empty($this->QUERY->where) && !empty($data)) {
                $this->update2($this->QUERY->from, $data, $this->QUERY->where);
            }
            return;
        } elseif (!is_string($table)) {
            throw new \Exception(Messages::PARAMETER_TYPE_ERROR);
        }
        $this->QUERY->from = $table;

        return new updateCallback($this);
    }

    protected function nonamedelete(string $field, $param)
    {
        if ($this->QUERY->safemode) {
            $this->update2($this->QUERY->from, [$this->CONFIG->safefield => 1], [$field => $param]);
        }
        //$this->delete($this->QUERY->from,$this->QUERY->safemode)->where([$field=>$param]);

    }

    protected function nonameset(string $field, $param): setCallback
    {
        $this->QUERY->pdo_parameters = [$field => $param];
        return new setCallback($this);
        //$this->update2($this->QUERY->from,[$field=>$param],[$field=>$param]);
    }

    protected function nonamewhere(string $field, $param): void
    {
        $this->where([$field => $param]);
    }

    protected function name()
    {

    }

    protected function from(...$arguments)
    {
        $this->QUERY->from = $arguments;
        return new fromCallback($this);
    }

    protected function where($where)
    {
        $where = $this->arrayFromObject($where);

        if ($this->QUERY->method == self::QUERY_TYPE_DELETE) {
            if ($this->QUERY->safemode) {
                $this->update2($this->QUERY->from, [$this->CONFIG->safefield => 1], $where);
            } else {
                //TODO:
                $query = $this->makeQuery();
            }
        }
        if ($this->QUERY->method == self::QUERY_TYPE_UPDATE) {
            $this->update2($this->QUERY->from, $this->QUERY->pdo_parameters, $where);
        }
        if ($this->QUERY->method == self::QUERY_TYPE_SELECT) {
            $this->QUERY->where = $where;
            return new whereCallback($this);
        }
        $this->QUERY->where = $where;
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
            return $this->fromDatabase2($this->QUERY->sql_query, $type, $this->QUERY->pdo_parameters);
        } elseif($this->QUERY->method == self::QUERY_TYPE_SELECT) {
            $query = $this->makeQuery();
            return $this->fromDatabase2($query->query,$type,$query->pdo_parameters);
        }
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
        //echo "sid:".Functions::get_random_string(20)."\n";
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }
    }
}