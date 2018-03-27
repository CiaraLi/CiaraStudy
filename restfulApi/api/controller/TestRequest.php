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
class TestRequest extends Request {

    function __construct() {
        
    }

    function auth() {
        $token = $this->postData('apitoken');
        $appid = $this->postData('appid');

        $apps = new Apps();
        if (!empty($apps)) {
            $data = $apps->chekAppCode($appid, $token);
        } else {
            $data = -1;
        }
        if ($data < 0) {
            die('Access denied !');
        }
    }

    function getTest($param = null) {
        $mysql = mysqlClass::getInstance();
        $sql = 'select * from ' . TBNAME;
        if (!empty($param)) {
            $sql.=' where id ="' . intval($param) . '"';
        }
        return $mysql->getAll($sql);
    }

    function postTest() {
        $this->auth();
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

}
