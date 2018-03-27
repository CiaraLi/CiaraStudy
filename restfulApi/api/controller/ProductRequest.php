<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductRequest
 *
 * @author chenglongliu
 */
class ProductRequest extends Request {

    function getProduct($param = null) {
        $mysql = mysqlClass::getInstance();
        $sql = 'select * from ' . TBNAME;
        if (!empty($param)) {
            $sql.=' where id ="' . intval($param) . '"';
        }
        return $mysql->getAll($sql);
    }

    function postProduct() {
        $mysql = mysqlClass::getInstance();
        if (!empty($this->postData('title')) && !empty($this->postData('isbn'))) {
            $title = addslashes($this->postData('title'));
            $isbn = addslashes($this->postData('isbn'));
            $mysql->setQuery('insert into ' . TBNAME . ' set isbn="' . $isbn . '" ,title="' . $title . '"');
            return $mysql->getbyid(TBNAME);
        } else {
            return ['code' => -1, 'error' => 'invalid params'];
        }
    }

    function putProduct($param = null) {
        $mysql = mysqlClass::getInstance();

        $put = self::$postData;
        if (!empty($param) && !empty($put['title']) && !empty($put['isbn'])) {
            $title = addslashes($put['title']);
            $isbn = addslashes($put['isbn']);
            $mysql->setQuery('update ' . TBNAME . ' set isbn="' . $isbn . '" ,title="' . $title . '"'
                    . ' where id="' . intval($param) . '"');
            return $mysql->getbyid(TBNAME, intval($param));
        } else {
            return ['code' => -1, 'error' => 'invalid params'];
        }
    }

    function deleteProduct($param = null) {
        $mysql = mysqlClass::getInstance();
        $sql = 'delete  from ' . TBNAME;
        $sql.=' where id ="' . intval($param) . '"';

        return $mysql->setQuery($sql);
    }

    function patchProduct() {
        
    }

}
