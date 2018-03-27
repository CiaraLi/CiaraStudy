<?php

include './api/config.php';
include './api/lib/Times.php';
include './api/lib/MysqlClass.php';
include './api/lib/Apps.php';
include './api/restful/Request.php';
include './api/restful/Response.php';

define('SUCCESS', 1);
define('NO_CONTENT', 0);
define('CREATED', 2);
define('UPDATED', 3);
define('DELETED', 4);
define('EXISTS', 5);
define('BAD_REQUEST', -401);
define('UNAUTHORIZED', -402);
define('ERROR_TOKEN', -403);
define('NOT_FOUND', -404);
define('UNKNOEN_ERROR', -500);
define('SERROR_EXCEPTION', -503);

$path = './api/controller/';
$array = ['post', 'get', 'put', 'delete', 'patch'];

$request = new Request();
$response = new Response();
$method = empty($_SERVER['REQUEST_METHOD']) ? "" : $_SERVER['REQUEST_METHOD'];
$method = strtolower($method);
if (in_array($method, $array)) {

    $class = $request->postData('class');
    if (file_exists($path . $class . 'Request.php')) {
        include_once $path . $class . 'Request.php';
        $classname = $class . "Request";
    } else {
        $class = $classname = 'Request';
    }
    $c = new $classname();
    $param = $request->postData('param');
    if (method_exists($classname, $method . $class)) {
        $func = $method . $class;
        if (!empty($param)) {
            $data = $c->$func($param);
        } else {
            $data = $c->$func();
        }
        return $response->parseData($data);
    } else {
        $data = 'error method';
        return $response->parseData($data, '404');
    }
}

