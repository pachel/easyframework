<?php

namespace Pachel\EasyFrameWork\DB\Modells;

use Pachel\EasyFrameWork\DB\mySql;

final class queryMaker
{
    /**
     * Az összerakott mysql parancs
     * @var string $query
     */
    public string $query = "";
    /**
     * HA van paraméter, akkor az van itt
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
        $sql .= $this->makeWhere();
        $this->query = $sql;
    }

    private function makeUpdate(Query &$query): string
    {

    }
    private function makeWhere():string{
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
        return $sql;

    }
//TODO: kell egy where maker is
    private function makeDelete(Query &$query)
    {

        $sql = "DELETE FROM " . $query->from . " WHERE ";
        $index = 0;
        $sql .= $this->makeWhere();
        $this->query = $sql;
    }

    private function getTable($data): string
    {
        return "";
    }

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

        return $return;
    }
}