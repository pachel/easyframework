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
use Pachel\EasyFrameWork\DB\Models\Config;
use Pachel\EasyFrameWork\DB\Models\dataModel;
use Pachel\EasyFrameWork\DB\Models\queryMaker;
use Pachel\EasyFrameWork\DB\Models\Query;
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

    /**
     * @param array|string ...$arguments
     * @return selectCallback
     */

    public function select(object $object): selectCallback
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

    protected function exec():bool
    {
        return $this->toDatabase($this->QUERY->sql_query, $this->QUERY->pdo_parameters);
    }

    protected function set(array $data)
    {
        if ($this->QUERY->method == self::QUERY_TYPE_INSERT) {
            return $this->insert2($this->QUERY->from, $this->arrayFromObject($data));
        }
        $this->QUERY->pdo_parameters = $this->arrayFromObject($data);
        return new setCallback($this);
    }


    protected function arrayFromObject($data)
    {
        if (!is_object($data)) {
            return $data;
        }
        if (method_exists($data, "tableName")) {
            $this->QUERY->from = $data->tableName();
            /**
             * A modelben beállított értékeket kell betölteni
             */
            $this->QUERY->safemode = $data->safeMode();
            $this->QUERY->safefield = $data->safeField();
            $primary = $data->primaryName();
            $delete = "";
            if (isset($data->{$primary})) {
                $delete = $primary;
            }
            /**
             * Ha van primary akkor az a where feltétel, és gégrehajtódik a meghívás után
             */
            if (isset($data->{$primary})) {
                $this->QUERY->where = [$primary => $data->{$primary}];

            } elseif (!isset($data->{$primary}) && $this->QUERY->method == self::QUERY_TYPE_UPDATE) {
                //throw new \Exception(Messages::MYSQL_OBJECT_UPDATE_NOT_ALLOWED[0], Messages::MYSQL_OBJECT_UPDATE_NOT_ALLOWED[1]);
                /*} elseif (!isset($data->{$primary}) && $this->QUERY->method == self::QUERY_TYPE_DELETE && !$this->QUERY->safemode) {
                    throw new \Exception(Messages::MYSQL_OBJECT_DELETE_NOT_ALLOWED[0], Messages::MYSQL_OBJECT_DELETE_NOT_ALLOWED[1]);*/
            } elseif ($this->QUERY->method == self::QUERY_TYPE_DELETE && isset($data->{$primary})) {
                /*
                $this->QUERY->where = [$primary => $data->{$primary}];
                foreach ($data as $key => $value) {
                    $this->QUERY->where[$key] = $value;
                }*/
            }
        }
        else{
            throw new \Exception(Messages::PARAMETER_TYPE_ERROR);
        }

        $vars = get_class_vars($data->className());
        $array = [];
        foreach ($vars as $key => $value) {
            if ($this->QUERY->method == self::QUERY_TYPE_SELECT) {
                if ($data->isVisible($key)) {
                    $array[$key] = "0";
                }
            } elseif (isset($data->{$key}) && $delete != $key) {
                $array[$key] = $data->{$key};
            }
            //$array[$key] = $value;
        }
        //    print_r($array);
        return $array;
    }

    protected function id(int $id)
    {
        //return $this->where(["id"=>$id]);
        //file_put_contents(__DIR__."/../../../examples/logs/unit.txt","sd",FILE_APPEND);
        $this->QUERY->where = ["id" => $id];

        if ($this->QUERY->method == self::QUERY_TYPE_DELETE) {
            if ($this->QUERY->safemode) {
                return $this->update2($this->QUERY->from, [$this->CONFIG->safefield => 1], ["id" => $id]);
            } else {
                //TODO:
                $query = $this->makeQuery();
                return $this->toDatabase($query->query,$query->pdo_parameters);

            }
        } elseif ($this->QUERY->method == self::QUERY_TYPE_UPDATE) {
            return $this->update2($this->QUERY->from, $this->QUERY->pdo_parameters, $this->QUERY->where);
        } elseif ($this->QUERY->method == self::QUERY_TYPE_SELECT) {
            return new idCallback($this);
        }

    }

    public function insert(object $table)
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_INSERT;
     //   if (is_object($table)) {
            $data = $this->arrayFromObject($table);
            if (!empty($data)) {
                return $this->insert2($this->QUERY->from, $data);
            }
            return false;
        /*} elseif (!is_string($table)) {
            throw new \Exception(Messages::PARAMETER_TYPE_ERROR);
        }
        $this->QUERY->from = $table;
        return new insertCallback($this);*/
    }

    public function delete( $table, $safe = null)
    {
        $param = false;
        if (!is_bool($safe)) {
            $safe = $this->CONFIG->safemode;
            $param = true;
        }
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_DELETE;
        $this->QUERY->safemode = $safe;

        if (is_object($table)) {
            $this->arrayFromObject($table);
            if (!empty($this->QUERY->where)) {
                $safemode = ($param?$safe:$this->QUERY->safemode);
                if ($safemode) {
                    $this->update2($this->QUERY->from, [$this->QUERY->safefield => 1], $this->QUERY->where);
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


    /**
     * Az update használható egy DataModel objektumból szármartatott másik osztály paramtéterként
     * átadásával, így nem kell semilyen más feltételt megadnunk, egyszerűen csak szerepelnie kell
     * az elsődleges kulcsnak az elemben
     * @see https://github.com/pachel/easyframework#mysql
     * @param strign|dataModel|bool $table
     * @return updateCallback|void
     * @throws \Exception
     */
    public function update($table)
    {
        $this->QUERY = new Query();
        $this->QUERY->method = self::QUERY_TYPE_UPDATE;

        if (is_object($table)) {
            $data = $this->arrayFromObject($table);
            if (!empty($this->QUERY->where) && !empty($data)) {
                return $this->update2($this->QUERY->from, $data, $this->QUERY->where);
            }
            /**
             * Ha nem üres az átadott objektum, de nincs primary id, akkor meg kell adni a feltételt
             */
            elseif (!empty($data)) {
                $this->QUERY->pdo_parameters = $data;
                return new setCallback($this);
            } else {
                goto endupdate;
                //throw new \Exception();
            }
            return;
        } elseif (!is_string($table)) {
            throw new \Exception(Messages::PARAMETER_TYPE_ERROR);
        }
        $this->QUERY->from = $table;
        endupdate:
        return new updateCallback($this);
    }

    protected function nonamedelete(string $field, $param)
    {

        //return $this->where([$field=>$param]);
        if ($this->QUERY->safemode) {
            $this->update2($this->QUERY->from, [$this->CONFIG->safefield => 1], [$field => $param]);
        } else {
            if (empty($param)) {
                throw new \Exception(Messages::MYSQL_NOT_ALLOWED_WITHOUT_WHERE[0], Messages::MYSQL_OBJECT_DELETE_NOT_ALLOWED[1]);
            }
            $this->QUERY->where = [$field => $param];
            $query = $this->makeQuery();
            $this->toDatabase($query->query, $query->pdo_parameters);
        }
    }

    /**
     * Arra jó, hogy az UPDATE parancsal bármelyik mezőnévre egy metódusként tudunk
     * hivatkozni és paraméterként be tudjuk állítani az értékét
     * @param string $field
     * @param mixed $param
     * @return setCallback
     * @example update("users")->name("john Do")->id(1)
     */
    protected function nonameset(string $field, $param)
    {
        // $this->QUERY->pdo_parameters = [$field => $param];
        return $this->set([$field => $param]);
        //return new setCallback($this);
        //$this->update2($this->QUERY->from,[$field=>$param],[$field=>$param]);
    }

    /**
     * Ez is olyan tip-top kis metódus, mint a nonameset, csak ez a where kiváltására használható!
     * @param string $field
     * @param $param
     */
    protected function nonamewhere(string $field, $param)
    {
        return $this->where([$field => $param]);
        /*
      //  $this->where([$field => $param]);
        if($this->QUERY->method == self::QUERY_TYPE_SELECT){
            $this->QUERY->where = [$field=>$param];
            return new whereCallback($this);
        }
        elseif ($this->QUERY->method == self::QUERY_TYPE_DELETE){
            //todo:
        }
        elseif($this->QUERY->method == self::QUERY_TYPE_UPDATE){

        }*/
    }

    protected function from(...$arguments)
    {
        $this->QUERY->from = $arguments;
        return new fromCallback($this);
    }

    protected function where($where)
    {
        $this->QUERY->where = $where;
        //$where = $this->arrayFromObject($where);

        if(is_array($where)){
            $this->QUERY->pdo_parameters = array_merge($this->QUERY->pdo_parameters,$where);
        }

        if ($this->QUERY->method == self::QUERY_TYPE_SELECT) {
            $this->QUERY->where = $where;
            return new whereCallback($this);
        }
        else{
            if (isset($this->QUERY->safemode) && $this->QUERY->safemode) {
                $this->QUERY->method = self::QUERY_TYPE_UPDATE;
            }

            $query = $this->makeQuery();
            return $this->toDatabase($query->query, $query->pdo_parameters);
        }
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
        } elseif ($this->QUERY->method == self::QUERY_TYPE_SELECT) {
            $query = $this->makeQuery();
            return $this->fromDatabase2($query->query, $type, $query->pdo_parameters);
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