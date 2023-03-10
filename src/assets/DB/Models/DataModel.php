<?php

namespace Pachel\EasyFrameWork\DB\Modells;

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

    public function __construct()
    {
        if (is_null($this->_tablename)) {
            $this->_tablename = $this->setTableName(get_called_class());
            $this->_classname = get_called_class();
        }
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
    public function tableName(): string
    {
        return $this->_tablename;
    }

    /**
     * Visszaadja a beállított elsődleges kulcsot!
     * @return string
     */
    public function primaryName(): string
    {
        return $this->_primary;
    }

    /**
     * Visszaadja az osztály nevét
     * @return string
     */
    public function className(): string
    {
        return $this->_classname;
    }

    public function isVisible(string $property_name): bool
    {
        if (in_array($property_name, $this->_not_visibles)) {
            return false;
        }
        return true;
    }

    /*
    public function __call(string $name, array $arguments)
    {
        if(method_exists($this,$name)){
            return $this->{$name}(...$arguments);
        }
    }
    */
}
