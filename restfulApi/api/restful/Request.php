<?php

class Request {

    public static $postData;


    function getRequest($param = null) {
        return array(
            'all products' => htmlentities(WEBURL . "api/product"),
            'show product' => html_entity_decode(WEBURL . "api/product/1")
        );
    }

    function postRequest($param = null) {
        return array(
            'all products' => htmlentities(WEBURL . "api/product"),
            'show product' => html_entity_decode(WEBURL . "api/product/1")
        );
    }

    function postData($key='') {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (in_array($method, ['put'])) {
            $input = file_get_contents("php://input"); //接收POST数据  
            $input = $this->parsePut($input);
            $post = array_merge($_REQUEST, $input);
            return empty($post[$key]) ? "" : $post[$key];
        } else {
            return empty($_REQUEST[$key]) ? "" : $_REQUEST[$key];
        }
    }

    function parsePut($put) {
        $match = array();
        $str = "/(name=\"(.*)\"(.*)[-])+/isU";
        preg_match_all($str, $put, $match);
        $param = array();
        if (!empty($match[1])) {
            foreach ($match[1] as $key => $value) {
                $param[$match[2][$key]] = trim($match[3][$key]);
            }
        }
        return self::$postData = $param;
    }

}
