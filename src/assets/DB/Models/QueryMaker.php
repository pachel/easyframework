<?php

namespace Pachel\EasyFrameWork\DB\Modells;

use Pachel\EasyFrameWork\DB\mySql;
use Pachel\EasyFrameWork\Messages;

/**
 * Ez az objektum készíti el a MYSQL szövegét, a mentett adatok alapján
 */
final class queryMaker
{
    /**
     * Az összerakott mysql parancs, ebben tároljuk
     * @var string $query
     */
    public string $query = "";
    /**
     * HA van PDO paraméter, akkor az van itt
     * @var array $pdo_parameters
     */
    public array $pdo_parameters = [];


    public function __construct(Query $query)
    {
        switch ($query->method) {
            case mySql::QUERY_TYPE_SELECT:
                $this->makeSelect($query);
                break;
            case mySql::QUERY_TYPE_DELETE:
                $this->makeDelete($query);
                break;
            case mySql::QUERY_TYPE_UPDATE:
                $this->makeUpdate($query);
                break;
        }
    }

    /**
     * Elkészíti a SELECT lekérdezést tartalmát
     * @param Query $query
     * @return void
     */
    private function makeSelect(Query &$query): void
    {
        $sql = "SELECT ";
        if ($query->select == "*" || empty($query->select)) {
            $sql .= "* ";
        } else {
            $index = 0;
            foreach ($query->select as $counter => $item) {
                if ($index > 0) {
                    $sql .= ",";
                }
                $sql .= "`" . $counter . "`";
                $index++;
//                $sql .= $this->getCol($item);
                //$sql .= $counter;
            }
        }
        $sql .= " FROM ";
        if (is_string($query->from)) {
            $sql .= "`" . $query->from . "` ";
        } else {
            foreach ($query->from as $index => $table) {
                if ($counter > 0) {
                    $sql .= ",";
                }
                $sql .= $this->getTable($table);
            }
        }
        $sql .= $this->makeWhere($query);
        $this->query = $sql;
    }

    /**
     * Az UPDATE parancs létrehozásához kell
     * @param Query $query
     * @return string
     */
    private function makeUpdate(Query &$query): void
    {
        $this->query = "UPDATE ".$query->from." SET ";
        $counter = 0;
        foreach ($query->pdo_parameters AS $key => $value){
            if($counter>0){
                $this->query.=",";
            }
            $this->query.="`".$key."`=:".$key;
            $counter++;
        }
        $this->query.=$this->makeWhere($query);
        $this->pdo_parameters = $query->pdo_parameters;
    }

    /**
     * A lekérdezés WHERE részét generálja le
     * @return string
     */
    private function makeWhere($query):string{
        $sql = "";
        if (!empty($query->where)) {
            $sql = " WHERE ";
            if (is_array($query->where)) {
                $counter = 0;
                foreach ($query->where as $key => $value) {
                    if ($counter > 0) {
                        $sql .= " AND ";
                    }
                    $sql .= "`" . $key . "`=:" . $key;
                    $this->pdo_parameters[$key] = $value;
                    $counter++;
                }
            }
            elseif (is_string($query->where)){
                $sql.= $query->where;
            }
        }
        else{
            throw new \Exception(Messages::MYSQL_WHERE_IS_EMPTY[0],Messages::MYSQL_WHERE_IS_EMPTY[1]);
        }
        return $sql;

    }


    private function makeDelete(Query &$query)
    {

        $sql = "DELETE FROM " . $query->from . " WHERE ";
        $index = 0;
        $sql .= $this->makeWhere($query);
        $this->query = $sql;
    }

    private function getTable($data): string
    {
        return "";
    }

    /**
     * Az mező felsorolást állítja össze, itt van egy csomó faszság, ami nem is kell
     * de majd ezeket kiszanáljuk a kódból
     * @param $data
     * @return string
     */
    private function getCol($data): string
    {
        $return = "";
        if (is_string($data)) {

            if (preg_match_all("/([^,]+)/", $data, $preg)) {
                $return = "";
                foreach ($preg[1] as $counter => $item) {
                    if ($counter > 0) {
                        $return .= ",";
                    }
                    if (preg_match("/([a-z0-9_]+)\.([a-z0-9_]+)/i", $item, $preg2)) {
                        $item = "";
                        //print_r($preg2);
                        for ($x = 1; $x < count($preg2); $x++) {
                            if ($x > 1) {
                                $item .= ".";
                            }
                            if (!preg_match("/`/", $preg2[$x])) {
                                $item .= "`" . $preg2[$x] . "`";
                            }
                        }
//                        echo $item."\n";
                    } elseif (!preg_match("/`/", $item)) {
                        $item = "`" . $item . "`";
                    }
                    $return .= $item . " ";
                }
            }
        } elseif (is_array($data)) {
            $return = $data[0] . " AS `" . $data[1] . "` ";
        }
        else{
            /**
             * Ha nem string és nem is array a paraméter tipusa, akkor errort dobunk
             */
            throw new \Exception(Messages::PARAMETER_TYPE_ERROR[0],Messages::PARAMETER_TYPE_ERROR[1]);
        }

        return $return;
    }
}