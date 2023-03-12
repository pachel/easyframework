<?php

namespace Pachel\EasyFrameWork\DB\Modells;

use mysql_xdevapi\Schema;
use Pachel\EasyFrameWork\Base;
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
    protected $_tablename = null;
    /**
     *
     * @var string $_primary
     */
    protected string $_primary = "id";
    protected array $_not_visibles = [];
    protected string $_classname = dataModel::class;
    private mySql $_db;

    public function __construct($values = null)
    {
        if (is_null($this->_tablename)) {
            $this->_tablename = $this->setTableName(get_called_class());
        }
        $this->_classname = get_called_class();
        if(!empty($values) && (is_array($values) || is_object($values))){
            foreach ($values AS $key => $value){
                if(property_exists($this,$key)) {
                    $this->{$key} = $value;
                }
                else{
                    throw new \Exception(sprintf(Messages::MODEL_PROPERY_NOT_EXISTS[0],$key,$this->_classname),Messages::MODEL_PROPERY_NOT_EXISTS[1]);
                }
            }
        }
        $this->_db =  clone Base::instance()->DB;

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

    protected function isVisible(string $property_name): bool
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
    public function getById(int $id){
        return $this->_db->select(new $this->_classname())->id($id)->line();
    }

    /**
     * @param string|array|object $where
     * @return whereCallback
     */
    public function select($where):whereCallback{
        $this->_db->select(new $this->_classname())->where($where);
        return new whereCallback($this->_db);
    }

    public function __call(string $name, array $arguments)
    {
        if(method_exists($this,$name)){
            return $this->$name(...$arguments);
        }
    }
}
