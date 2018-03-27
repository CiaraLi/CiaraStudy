<?php

define('AUTHCODE', '7znZt2oJ');
define('SECRETKEY', '123456');
define('APPID', '1');
define('APIURL', 'http://localhost:83/CiaraStudy/restfulApi/');

require_once './lib/Api.php';  //����
require_once './lib/Times.php'; //����
require_once './lib/PHPRedis.php';
require_once './lib/func.php';
require_once './lib/Curl.php';
session_start();

$ch = Curl::getInstance();
$api = new Api();

$url = APIURL . 'api/token';
$token = $api->getToken($url, '1', Api::APIAuth_encode(AUTHCODE, SECRETKEY));
var_dump(' API ID:' . APPID, ' API token:' . $token);


$url = APIURL . 'api/test';
$data = [
    'appid' => APPID, "apitoken" => $token, 'title' => 'book' . rand(100, 999), 'isbn' => rand(1000000000, 9999999999)
];
$return = $ch->postdata($url, $data)->run();
var_dump($return);
$return = json_decode($return, TRUE);
if (!empty($return['id'])) {
    $url = APIURL . 'api/test/' . $return['id'];
    $return = $ch->getdata($url)->run();
    $return = json_decode($return, TRUE);
    showtable($return);
}
PHPRedis::set('user_login', $return);
var_dump(PHPRedis::get('user_login'));

