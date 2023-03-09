<?php

namespace Pachel\EasyFrameWork\DB\Interfaces;

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
            foreach ($query->select as $counter => $item) {
                if ($counter > 0) {
                    $sql .= ",";
                }
                $sql .= $this->getCol($item);
            }
        }
        $sql .= "FROM ";
        foreach ($query->from as $index => $table) {
            if ($counter > 0) {
                $sql .= ",";
            }
            $sql .= $this->getTable($table);
        }
        $this->query = $sql;
    }

    private function makeUpdate(Query &$query): string
    {

    }

    private function makeDelete(Query &$query)
    {
        $sql = "DELETE FROM ".$query->from." WHERE ";
        foreach ($query->where AS $index => $item){
            if(is_array($item)) {
                if ($index > 0) {
                    $sql .= "AND ";
                }
                $a = array_keys($item);
                $sql.=$a[0]."=".$item[$a[0]];
            }
        }
        $this->query = $sql;

    }

    private function getTable($data): string
    {
        return "";
    }

    private function getCol($data): string
    {
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
            $return = $data[0]." AS `".$data[1]."` ";
        }
        return $return;
    }
}