<?php

$nodes = [
    [// advanced way, array including username, password and socket options
        'host' => '127.0.0.1',
        'port' => 9042, //default 9042
        'username' => 'test',
        'password' => 'test',
        'socket' => [SO_RCVTIMEO => ["sec" => 10, "usec" => 0], //socket transport only
        ],
    ], 
];
//cassandra
define('CASS_NODES', json_encode($nodes));
define('KEYSPACE_NAME', 'books');

class cassClass {

    protected $tablename;
    protected $id;
    public $columns;
    protected $batch;
    protected $hiddens;

    function __construct($tablename) {
        $this->tablename = $tablename;
        $this->connect_cassa_db();
    }

    function __destruct() {
        if ($this->conn instanceof Cassandra\Connection) {
            $this->conn->disconnect();
        }
    }

    function connect_cassa_db() {
        // Create a connection.
        $this->conn = new Cassandra\Connection(json_decode(CASS_NODES, true), KEYSPACE_NAME);
        try {
            $this->conn->connect();
        } catch (Cassandra\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            exit; //if connect failed it may be good idea not to continue
        }
    }

    function create_uuid($prefix = "") {    //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 8) . '-';
        $uuid .= substr($str, 8, 4) . '-';
        $uuid .= substr($str, 12, 4) . '-';
        $uuid .= substr($str, 16, 4) . '-';
        $uuid .= substr($str, 20, 12);
        return $prefix . $uuid;
    }

    function prseUuid($uuid = null) {
        //82ef2751-dc11-4956-a72c-31dc70a202f2
        return new Cassandra\Type\Uuid(empty($uuid) ? $this->create_uuid() : $uuid);
    }

    function prseTimeuuid($uuid = null) {
        //1409830696263 1496307998641000 2017-06-01 05:06:38+0800  
        return new Cassandra\Type\Uuid(empty($uuid) ? $this->create_uuid() : $uuid);
    }

    function prseTimestamp($val = null) {
        //1409830696263 1496307998641000 2017-06-01 05:06:38+0800  
        return new Cassandra\Type\Timestamp(empty($val) ? ((int) (microtime(true) * 1000)) : $val * 1000);
    }

    function prseFloat($val) {
        //1409830696263 1496307998641000 2017-06-01 05:06:38+0800
        return new Cassandra\Type\PhpFloat(floatval($val));
    }

    function prseInt($val) {
        //1409830696263 1496307998641000 2017-06-01 05:06:38+0800
        return new Cassandra\Type\PhpInt($val);
    }

    function allcolumns() {
        try {
            $cql = "SELECT column_name FROM  system_schema.columns  WHERE keyspace_name = '" . KEYSPACE_NAME . "' AND table_name = '" . $this->tablename . "'";
            $response = $this->conn->querySync($cql);
        } catch (Cassandra\Exception $e) {
            throw new Exception($e->getMessage());
        }
        $rows = $response->fetchAll();
        $columns = array();
        foreach ($rows as $key => $value) {
            $columns[] = $value['column_name'];
        }
        return $columns;
    }

    function checkExists($coloum, $value, $id) {
        try {
            $cql = "SELECT count(*) FROM " . $this->tablename . " where solr_query = ''";
            $where = trim($coloum) . ":" . trim($value);
            empty($id) ? null : $where .= " && " . trim($this->id) . ":" . trim($id) . "";
            $cql .= $where . " '";
            $response = $this->conn->querySync($cql);
        } catch (Cassandra\Exception $e) {
            throw new Exception($e->getMessage());
        }
        $rows = $response->fetchAll();
        $columns = array();
        foreach ($rows as $key => $value) {
            $columns[] = $value['column_name'];
        }
        return $columns;
    }

    function likestr($value, $perpage) {
        if (preg_match("/[\x7f-\xff]/", $value)) {
            $like = ($perpage === false ? '"%' . $value . '%"' : '\"%' . $value . '%\"');
        } else {
            $like = '*' . $value . '*';
        }
        return $like;
    }

    function fetchAll($datas=[]) {
        try {
            $param = [];
            $where = '';
            if (!empty($datas)) {
                foreach ($datas as $key => $value) {
                    $columns[] = $key . '=:' . $key;
                    $param[$key] = $value;
                }
                $where = ' where ' . implode('&&', $columns);
            }
            echo $cql = "SELECT  * FROM " . $this->tablename . $where;

            $preparedData = $this->conn->prepare($cql);
            $response = $this->conn->executeSync(
                    $preparedData['id'], $param, null, [//Cassandra\Request\Request::CONSISTENCY_LOCAL_SERIAL
                'page_size' => 20,
                'names_for_values' => true,
                'skip_metadata' => true,
                    ]
            );
            $response->setMetadata($preparedData['result_metadata']);
        } catch (Cassandra\Exception $e) {
            throw new Exception($e->getMessage());
        }
        $rows = $response->fetchAll();
        return $rows;
    }

    function insert($datas) {
        try {
            $set = $val = [];
            foreach ($datas as $key => $value) {
                $set[] = $key;
                $val[] = ':' . $key;
                $param[$key] = $value;
            }
            echo $cql = "INSERT  into  " . $this->tablename . '(' . implode(',', $set) . ') VALUES (' . implode(',', $val) . ')';
            $preparedData = $this->conn->prepare($cql);
            $response = $this->conn->executeSync(
                    $preparedData['id'], $param, null, [
                'page_size' => 100,
                'names_for_values' => true,
                'skip_metadata' => true,
                    ]
            );
        } catch (Cassandra\Exception $e) {
            throw new Exception($e->getMessage());
        } 
        return $response;
    }

}

//Connect



// Set consistency level for farther requests (default is CONSISTENCY_ONE)
//$connection->setConsistency(Request::CONSISTENCY_QUORUM);

// Run query synchronously.

/*1) yf_product_batch_import_logs
2) yf_product_batch_import_status 
3) yf_product_books
4) yf_product_freight
5) yf_product_logistics
6) yf_product_personalprice
7) yf_product_sales_status
8) yf_product_status
9) yf _product_tag WHERE AS.id=trans.id AS 
10) yf_product_translate AS trans
11) yf_product_units*/
