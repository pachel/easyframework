<?php

namespace Pachel\EasyFrameWork\DB\Traits;
use JetBrains\PhpStorm\Deprecated;
use \PDO;
trait OldTimerMethods
{
    #[Deprecated]
    public function fromDatabase($sql, $field = NULL, $params = [], $id = null)
    {

        if (!$sql) {
            throw new Exception('sql statement missing!');
        }

        if (is_array($field)) {
            $tmp = $params;
            $params = $field;
            $field = $tmp;
        }

        $resultArray = array();
        $this->check_params($params, $sql);
        $result = $this->PDO->prepare($sql);
        $result->execute($params);

        if ($field == '@flat') {
            if ($result->rowCount()) {
                while ($temp = $result->fetch(PDO::FETCH_NUM)) {
                    $resultArray[] = $temp;
                }
                return ($resultArray);
            } else {
                return [];
            }
        }
        if ($field == '@simple') {
            if ($result->rowCount()) {
                $temp = $result->fetch(PDO::FETCH_ASSOC);
                $resultArray = array_values($temp);
                return ($resultArray[0]);
            } else {
                return [];
            }
        }
        if ($field == '@line') {
            if ($result->rowCount()) {
                if ($this->object) {
                    $resultArray = $result->fetch(PDO::FETCH_OBJ);
                } else {
                    $resultArray = $result->fetch(PDO::FETCH_ASSOC);
                }
                return ($resultArray);
            } else {
                return [];
            }
        }
        if ($field == '@group' && $id != null) {
            if ($result->rowCount()) {
                while ($temp = $result->fetch(PDO::FETCH_ASSOC)) {
                    $resultArray[$temp[$id]] = $temp;
                }
                return ($resultArray);
            }
        }
        if ($field == '@array') {
            if ($result->rowCount()) {
                while ($temp = $result->fetch(PDO::FETCH_NUM)) {
                    $resultArray[] = $temp[0];
                }
                return ($resultArray);
            }
        }
        $i = 0;
        if ($result->rowCount()) {
            while ($temp = $result->fetch(PDO::FETCH_ASSOC)) {
                $resultArray[$i] = $temp;
                $i++;
            }
            return ($resultArray);
        }


        return [];
    }

    public function toDatabase($sql, $params = array())
    {
        $mysql_queryPrepared = $this->PDO->prepare($sql);
        $mysql_queryReturn = $mysql_queryPrepared->execute($params);
        //do we have a db error?
        $err = $mysql_queryReturn;
        if (!$err) {
            //error occured, show the error:
            $error = $mysql_queryPrepared->errorInfo();
            throw new Exception("MYSQL ERROR: " . $error[2] . "\n");
        }
        return (true);
    }

    private function arrayToDatabase($array, $table, $id = array())
    {
        if (!is_array($array)) {
            throw new Exception('$array is not Array()');
        }
        if (empty($table)) {
            throw new Exception('$table parameter is empty!');
        }
        if (gettype($table) != "string") {
            throw new Exception('$table parameter type is not string!');
        }
        $this->check_params($array);

        if (sizeof($id) == 0) {
            $query = "INSERT INTO `" . $table . "` (";
            $x = 0;
            foreach ($array as $key => $value) {
                if ($x > 0) {
                    $query .= ",";
                }
                $query .= "`" . $key . "`";
                $x++;
            }
            $query .= ") VALUES (";
            $x = 0;
            foreach ($array as $key => $value) {
                if ($x > 0) {
                    $query .= ",";
                }
                $query .= ":" . $key . "";
                $x++;
            }
            $query .= ")";
        } else {
            $query = "UPDATE `" . $table . "` SET ";
            $x = 0;
            foreach ($array as $key => $value) {
                if ($x > 0) {
                    $query .= ",";
                }
                $query .= "`" . $key . "`=:" . $key;
                $x++;
            }
            $k = array_keys($id);
            $query .= " WHERE " . $k[0] . "=:" . $k[0];
            $array[$k[0]] = $id[$k[0]];
        }

        return $this->toDatabase($query, $array);
    }

    private function check_params(&$data, &$query = null)
    {

        foreach ($data as $index => $item) {
            check:
            if (preg_match_all("/:" . $index . "/", $query, $preg)) {
                if (count($preg[0]) > 1) {
                    $new_name = $this->get_random_string();
                    $query = preg_replace("/:" . $index . "/", ":" . $new_name, $query, 1);
                    $data[$new_name] = $item;
                    goto check;
                }
            }
        }
        //echo $query;
        //print_r($data);
    }
    public function update($table, $data, $where)
    {
        return $this->arrayToDatabase($data, $table, $where);
    }

    /**
     * @param $table
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function insert($table, $data)
    {
        return $this->arrayToDatabase($data, $table);
    }

    /**
     * @param $table
     * @param $where
     */
    public function delete($table, $where)
    {
        $sql = "DELETE FROM `" . $table . "` WHERE " . $this->get_where($where);
        return $this->toDatabase($sql);
    }

    /**
     * @param $where
     * @param array $params
     * @return string
     */
    private function get_where($where, &$params = [])
    {
        $string = "";
        if (is_array($where)) {
            $counter = 0;
            foreach ($where as $index => $value) {
                if ($counter > 0) {
                    $string .= " AND ";
                }
                $string .= "`" . $index . "`" . (is_numeric($value) ? "=" . $value : " LIKE '" . $value . "'");
                $counter++;
            }
        } else {
            $string = $where;
        }
        return $string;
    }

    public function last_insert_id()
    {
        return $this->pdo->lastInsertId();
    }
}