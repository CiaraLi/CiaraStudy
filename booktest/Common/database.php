<?php

namespace DB;

class DataBase {

    protected $connt;
    protected $host;
    protected $username;
    protected $password;
    protected $batabase;
    protected $table;
    protected $fields;
    protected $order;
    public $datalist;

    function __construct() {
        $this->host = DBHOST;
        $this->username = USERNAME;
        $this->password = PASSWORD;
        $this->batabase = DATABASE;
        $this->Connect();
    }

    function __destruct() {
        $this->Close();
    }

    protected function init($host, $username, $password, $batabase) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->batabase = $batabase;
        return $this;
    }

    protected function Connect() {
        $this->connt = mysqli_connect($this->host, $this->username, $this->password, $this->batabase);
        if (!$this->connt) {
            die('Could not connect: ' . mysqli_error());
        }
        return $this;
    }

    protected function Close() {
        if ($this->connt) {
            mysqli_close($this->connt);
        }
    }

    protected function BuildSql($type, $where = array(), $update = array()) {
        switch ($type) {
            case 'insert':
                $sql = "insert into " . addslashes($this->table) . "  set ";
                if (is_array($update)) {
                    foreach ($update as $key => $val) {
                        $sql .= addslashes($key) . "='" . addslashes($val) . "'      , ";
                    }
                    $sql .= ' created_at="' . date("Y-m-d H:i:s", time()) . '" ,updated_at="' . date("Y-m-d H:i:s", time()) . '" ';
                } else {
                    $sql = "";
                }
                break;
            case 'delete':
                $sql = "delete from " . $this->table . "   where  "; 
                if (is_array($where)) {
                    foreach ($where as $key => $val) {
                        $sql .= addslashes($key) . "='" .addslashes($val ). "'      and ";
                    }
                    $sql = substr($sql, 0, strlen($sql) - 6);
                } else {
                    $sql = "";
                }
                break;
            case 'update':
                $sql = "update " . $this->table . "  set ";
                if (is_array($update)) {
                    foreach ($update as $key => $val) {
                        $sql .= addslashes($key) . "='" . addslashes($val ). "'      , ";
                    }
                    $sql .= '  updated_at="' . date("Y-m-d H:i:s", time()) . '"  where ';
                    if (is_array($where)) {
                        foreach ($where as $key => $val) {
                            $sql .= addslashes($key) . "='" . addslashes($val) . "'      and ";
                        }
                        $sql = substr($sql, 0, strlen($sql) - 6);
                    } else {
                        $sql .= "id='" . addslashes($val) . "' ";
                    }
                } else {
                    $sql = "";
                }
                break;
            case 'select':
                $sql = "select " . $this->GetFileds() . " from " . $this->table . " where ";
                if (is_array($where)) {
                    foreach ($where as $key => $val) {
                        $sql .= addslashes($key) . "='" . addslashes($val). "'      and ";
                    }
                    $sql = substr($sql, 0, strlen($sql) - 6);
                } else {
                    $sql .= $where;
                }
                if(!empty($update)){
                      foreach ($update as $key => $val) {
                        $sql .=" and " .addslashes($key) . "<>'" . addslashes($val ). "'  ";
                    }  
                }
                $sql .= " order by " . $this->order;
                break;

            default:
                $sql = "";
                break;
        }
        return $sql;
    }

    protected function DB($table) {
        $this->table = $table;
        $this->order = "created_at desc";
        $this->fields = [];
        return $this;
    }

    public function Filed($fileds = []) {
        $this->fields = $fileds;
        return $this;
    }

    protected function GetFileds($arr = false) {
        $sql = ' SHOW COLUMNS FROM ' . $this->table;
        $result = mysqli_query($this->connt, $sql);
        $flied = [];
        if($result){
        while ($row = mysqli_fetch_row($result)) {
            if (empty($this->fields) || in_array($row[0], $this->fields)) {
                $flied[] = $row[0];
            }
        }
        }
        return $arr ? $flied : implode(',', $flied);
    }

    protected function FetchToArray($result, $first = false) {
        $fileds = $this->GetFileds(1);
        $lists = [];
        while ($row = mysqli_fetch_row($result)) {
            foreach ($row as $key => $value) {
                $list[$fileds[$key]] = $value;
            }
            if ($first) {
                return $list;
            }
            $lists[] = $list;
        }
        return $lists;
    }

    protected function Order($order) {
        $this->order = $order;
    }

    public function Query($sql) {
        if ($result = mysqli_query($this->connt, $sql)) {
            if ($result->num_rows) {
                $this->datalist = $this->FetchToArray($result);
            }
        } 
        $this->fields = [];
        return $this;
    }

    protected function QueryFirst($where = []) {
        $sql = $this->BuildSql('select', $where);
        if ($result = mysqli_query($this->connt, $sql)) {
            if ($result->num_rows) {
                $this->datalist = $this->FetchToArray($result, 1);
            }
        } 
        $this->fields = [];
        return $this->datalist;
    }

    protected function QueryFetch($where = array()) {
        $sql = $this->BuildSql('select', $where);
        if ($result = mysqli_query($this->connt, $sql)) {
            if ($result->num_rows) {
                $this->datalist = $this->FetchToArray($result);
            }
        }
        $this->fields = [];
        return $this;
    }

    protected function isExists($where,$id=[]) {
        $sql = $this->BuildSql('select', $where,$id);
        if ($result = mysqli_query($this->connt, $sql)) {
            if ($result->num_rows) {
                return true;
            } else {
                return false;
            }
        }
        return $result;
    }

    protected function Insert($values) {
        $sql = $this->BuildSql('insert', [], $values);
        $result = mysqli_query($this->connt, $sql);
        return $result;
    }

    protected function Update($where, $values) {
        $sql = $this->BuildSql('update', $where, $values);
        $result = mysqli_query($this->connt, $sql);
        return $result;
    }

    protected function delete($where) {
        $sql = $this->BuildSql('delete', $where);
        $result = mysqli_query($this->connt, $sql);
        return $result;
    }

}
