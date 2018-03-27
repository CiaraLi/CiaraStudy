<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysqlClass
 *
 * @author iong
 */
class mysqlClass {

    static protected $conn;
    static protected $instance;
    static protected $tablename;
    static protected $id;
    static protected $server;
    static protected $user;
    static protected $pass;
    static protected $database;

    function __construct() {
        
    }

    static function getInstance() {
        self::$server = DBSERVER; // this may be an ip address instead
        self::$user = DBUSER;
        self::$pass = DBPASSWORD;
        self::$database = DBNAME;

        if (!self::$instance instanceof self) {
            self::$instance = new self();
            self::$conn = new mysqli(self::$server, self::$user, self::$pass, self::$database);
            if (mysqli_connect_errno()) {
                echo "连接数据库失败：" . mysqli_connect_error();
                self::$conn = null;
                exit;
            }
            $tbname = TBNAME;
//            $tables = <<<sql
//                    create table if not exists $tbname(id int(11) auto_increment primary key,title varchar(15),isbn varchar(15) unique);
//sql;
//            $table = mysqli_query(self::$conn, $tables);
//            self::$stmt = mysqli_stmt_init(self::$conn);
        }
        return self::$instance;
    }

    public function getFirstColumn($query) {
        $stmt = mysqli_query(self::$conn, $query);
        if ($stmt) {
            $row = mysqli_fetch_array($stmt);
        } else {
            $row = false;
        }
        return $row;
    }

    public function getFirstone($query) {
        $stmt = mysqli_query(self::$conn, $query);
        if ($stmt) {
            $row = mysqli_fetch_array($stmt);
        } else {
            $row = false;
        }
        return $row;
    }

    public function getAll($query) {
        $stmt = mysqli_query(self::$conn, $query);
        if ($stmt) {
            $rows = [];
            while ($row = mysqli_fetch_array($stmt)) {
                $rows[] = $row;
            }
        } else {
            $rows = false;
        }
        return empty($rows) ? FALSE : $rows;
    }

    public function setQuery($query) {
        mysqli_query(self::$conn, $query);
        self::$id = mysqli_insert_id(self::$conn);
        return mysqli_affected_rows(self::$conn);
    }

    public function getbyid($table, $id = "") {
        if (empty($id)) {
            $id = self::$id;
        }
        return $this->getFirstone('select * from ' . $table . ' where id="' . $id . '"');
    }

    public function getUpdateQuery($data, $where) {
        $mysql = '';
        if (!empty($data) && !empty($where)) {
            foreach ((array) $data as $key => $value) {
                $set[] = $key . '="' . addslashes($value) . '"';
            }
            $mysql = 'update ' . TBNAME . ' set  ' . implode(',', $set);

            foreach ((array) $where as $key => $value) {
                $set[] = $key . '="' . addslashes($value) . '"';
            }
            $mysql .= ' where ' . implode(' and ', $set);
        }

        return $mysql;
    }

    public function getInsertQuery($data, $where) {
        $mysql = '';
        if (!empty($data) && !empty($where)) {
            foreach ((array) $data as $key => $value) {
                $set[] = $key . '="' . addslashes($value) . '"';
            }
            $mysql = 'insert into ' . TBNAME . ' set  ' . implode(',', $set);
        }
        return $mysql;
    }

    public function getSelectQuery($data, $where) {
        $mysql = $where = '';
        if (!empty($data) && !empty($where)) {
            foreach ((array) $where as $key => $value) {
                $set[] = $key . '="' . addslashes($value) . '"';
            }
            $where = ' where  ' . implode(' and ', $set);
        }
        $mysql = 'select  ' . implode(',', $data) . ' from ' . TBNAME . $where;
        return $mysql;
    }

}
