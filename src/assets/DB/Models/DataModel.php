<?php

namespace Pachel\EasyFrameWork\DB\Models;

use Pachel\EasyFrameWork\Base;
use Pachel\EasyFrameWork\DB\callBacks\DataModel\makeUpdateCallback;
use Pachel\EasyFrameWork\DB\callBacks\DataModel\paramsCallback;
use Pachel\EasyFrameWork\DB\callBacks\DataModel\makeSelectCallback;
use Pachel\EasyFrameWork\DB\callBacks\setCallback;
use Pachel\EasyFrameWork\DB\callBacks\whereCallback;
use Pachel\EasyFrameWork\DB\mySql;
use Pachel\EasyFrameWork\Messages;

/**
 * @version 1.0
 * @author László Tóth
 * @see https://github.com/pachel/easyframework
 */
abstract class dataModel
{
    /**
     *
     * @var string|null $_tablename
     */
    protected string $_tablename = "";
    /**
     *
     * @var string $_primary
     */
    protected string $_primary = "id";
    /**
     * A SELECT lekérdezésben láthatatlan mezők nevei
     * @var array $_not_visibles
     */
    protected array $_not_visibles = [];

    protected string $_classname = dataModel::class;
    protected string $_modelclass = "";

    /**
     *
     * @var string $_safefield
     */
    protected string $_safefield;
    /**
     * @var bool $_safemode
     */
    protected bool $_safemode;
    /**
     * @var mySql $_db
     */
    private mySql $_db;
    public const
        QUERY_TYPE_SELECT = 1,
        QUERY_TYPE_DELETE = 2,
        QUERY_TYPE_UPDATE = 3,
        QUERY_TYPE_QUERY = 4,
        QUERY_TYPE_INSERT = 5;
    /**
     * @var Query $_query
     */
    private Query $_query;
    public function __construct($values = null)
    {
        if (empty($this->_tablename)) {
            $this->_tablename = $this->setTableName(get_called_class());
        }
        $this->_classname = get_called_class();
        if (!empty($values) && (is_array($values) || is_object($values))) {
            foreach ($values as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                } else {
                    throw new \Exception(sprintf(Messages::MODEL_PROPERY_NOT_EXISTS[0], $key, $this->_classname), Messages::MODEL_PROPERY_NOT_EXISTS[1]);
                }
            }
        }
        if (empty($this->_safefield)) {
            $this->_safefield = Base::instance()->env("mysql.safefield");
        }
        if (empty($this->_safemode)) {
            $this->_safemode = Base::instance()->env("mysql.safemode");
        }
        $this->_db = clone Base::instance()->DB;
    }

    /**
     *
     * @param string $class
     * @return string $table_name
     * @throws \Exception
     */
    private function setTableName(string $class): string
    {

        if (preg_match("/([^\\\]+)_Model$/", $class, $preg)) {
            $class = $preg[1];
        } else {
            throw new \Exception(sprintf(Messages::MYSQL_MODELL_NAME_INVALID[0], $class), Messages::MYSQL_MODELL_NAME_INVALID[1]);
        }
        return $class;
    }

    /**
     * Visszaadja a beállított tábla nevét, hogy a DB tudjon vele dolgozni
     * @return string
     */
    protected function tableName(): string
    {
        return $this->_tablename;
    }

    /**
     * Visszaadja a beállított elsődleges kulcsot!
     * @return string
     */
    protected function primaryName(): string
    {
        return $this->_primary;
    }

    /**
     * Visszaadja az osztály nevét
     * @return string
     */
    protected function className(): string
    {
        return $this->_classname;
    }

    protected function safeMode(): bool
    {
        return $this->_safemode;
    }

    protected function safeField(): string
    {
        return $this->_safefield;
    }

    private function isVisible(string $property_name): bool
    {
        if (in_array($property_name, $this->_not_visibles)) {
            return false;
        }
        return true;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->_db->select(new $this->_classname())->id($id)->line();
    }
    /**
     * @param string|array|object $where
     * @return whereCallback|null
     */
    public function select($where):whereCallback
    {
        $this->newQuery(true);
        $this->_query->method = self::QUERY_TYPE_SELECT;
        $this->_query->where = $where;
        return new whereCallback($this);
    }

    /**
     * @param $data
     * @return bool|setCallback
     * @throws \Exception
     */
    public function update($data)
    {
        $where = [];
        $data = $this->_dataToArray($data,false,$where);
        if(empty($data)){
            throw new \Exception(Messages::MYSQL_SET_IS_EMPTY[0],Messages::MYSQL_SET_IS_EMPTY[1]);
        }

    }

    public function lastInsertId(){
        return $this->_db->last_insert_id();
    }
    public function insert($data):bool
    {
        $data = $this->_dataToArray($data);
        if(empty($data)){
            throw new \Exception(Messages::MYSQL_SET_IS_EMPTY[0],Messages::MYSQL_SET_IS_EMPTY[1]);
        }
        return $this->_db->insert($this->_tablename,$data);
    }
    private function _dataToArray($data,$isWhere=false,array &$primary = []):array
    {
        if(is_object($data)){
            $data = get_object_vars($data);
        }
        $delete = false;
        foreach ($data AS $kex => $value){
            if($kex == $this->_primary && !$isWhere){
                $primary = [$kex=>$value];
                $delete = true;
            }
        }
        if($delete){
            unset($data[$this->_primary]);
        }
        return $data;
    }
    public function deleteById(int $id,$safe = null){
        if(!is_bool($safe)){
            $safe = $this->_safemode;
        }
        if($safe){
            return $this->_db->update($this->_tablename,[$this->_safefield=>1],[$this->_primary=>$id]);
        }
        return $this->_db->query("DELETE FROM `".$this->_tablename."` WHERE `".$this->_primary."`=:id")->params(["id"=>$id])->exec();
    }

    /**
     * @param $where
     * @param $safe
     * @return bool
     * @throws \Exception
     */
    public function delete(array $where,$safe = null):bool
    {
        if(!is_bool($safe)){
            $safe = $this->_safemode;
        }

        if($safe){
            return $this->_db->update($this->_tablename,[$this->_safefield=>1],$where);
        }
        $query = $this->newQuery($safe);
        $query ->method = self::QUERY_TYPE_DELETE;
        $query->where = $where;
        $query = $this->makeQuery();
        return  $this->_db->toDatabase($query->query,$query->pdo_parameters);
       // return $this->_db->delete(new $this->_classname(),$safe)->where($where);
    }
    private function newQuery(bool $safe):Query{
        $query = new Query();
        $query->safefield = $this->_safefield;
        $query->safemode = $safe;
        $query->from = $this->_tablename;
        return $query;
    }
    protected function _nonameWhere(){

    }
    /**
     * @param array $data Associatív tömb kell, hoyg legyen, vagy stdObject
     * @return paramsCallback
     */
    public function params(array $data):paramsCallback{
        //TODO: DELETE UPDATE SELECT csakis string paraméterrel
        $this->_query = new Query();
        $this->_query->from = $this->_tablename;
        $this->_query->safemode = $this->_safemode;
        $this->_query->safefield = $this->_safefield;
        $this->_query->pdo_parameters = $this->_dataToArray($data,true);
        return new paramsCallback($this);
    }
    protected function makeUpdate(string $query):makeUpdateCallback{
        $this->_query->where = $query;
        $this->_query->method = self::QUERY_TYPE_UPDATE;
        return new makeUpdateCallback($this);

    }
    protected function makeSelect(string $query):makeSelectCallback{
        $this->_query->method = self::QUERY_TYPE_SELECT;
        $this->_query->where = $query;
        return new makeSelectCallback($this);
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
        if ($this->_query->method == self::QUERY_TYPE_SELECT) {
            $query = $this->makeQuery();
            return $this->_db->fromDatabase($query->query, $type, $query->pdo_parameters);
        }
        return [];
    }
    protected function set($values):bool{
        $this->_query->set = $this->_dataToArray($values);
        $query = $this->makeQuery();
        return $this->_db->toDatabase($query->query,$query->pdo_parameters);
    }
    private function makeQuery(){
        return new queryMaker($this->_query);
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
