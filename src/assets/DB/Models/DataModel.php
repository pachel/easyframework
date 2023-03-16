<?php

namespace Pachel\EasyFrameWork\DB\Models;

use Pachel\EasyFrameWork\Base;
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
    protected array $_not_visibles = [];
    protected string $_classname = dataModel::class;

    protected string $_safefield;
    protected bool $_safemode;
    private mySql $_db;

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
    public function getById(int $id)
    {
        return $this->_db->select(new $this->_classname())->id($id)->line();
    }

    public function set(array $set):setCallback
    {
        return $this->_db->update($this->_tablename)->set($set);
    }

    public function getByEmail(string $email)
    {
        return $this->_db->select(new $this->_classname())->email($email)->line();
    }

    /**
     * @param string|array|object $where
     * @return whereCallback
     */
    public function select($where): whereCallback
    {
        $this->_db->select(new $this->_classname())->where($where);
        return new whereCallback($this->_db);
    }
    public function update(){

    }
    public function insert(){

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
