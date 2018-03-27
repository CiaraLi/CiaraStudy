<?php

define('APIURL', 'http://localhost:83/CiaraStudy/restfulApi/');
require_once './client/lib/Curl.php';

$ch = Curl::getInstance();

$url = APIURL . 'api/product';
$rand = rand();
$ch->postdata($url, array('isbn' => '1' . $rand, 'title' => 'book' . $rand));
print_r("\r\n<br/>---添加图书:" . 'book' . $rand . "--- \r\n<br/>");
echo $ch->run();


$url = APIURL . 'api/product';
$ch->getdata($url);
print_r("\r\n<br/>---图书列表--- \r\n<br/>");
echo $json = $ch->run();

$list = json_decode($json, true);
//var_dump($list);

if (count($list)) {
    $rand = $list[rand(0, count($list) - 1)]['id'];
    $url = APIURL . 'api/product/' . intval($rand);
    $data = array('isbn' => '1' . $rand, 'title' => 'book' . $rand);
    $ch->putdata($url, ($data));
    print_r("\r\n<br/>---修改图书:$rand --- \r\n<br/>");
    echo $ch->run();

    $rand = rand(-1, count($list) - 1);
    if ($rand > 0) {
        $id = $list[$rand]['id'];
        $url = APIURL . 'api/product/' . intval($id);
        $ch->deldata($url);
        print_r("\r\n<br/>---删除图书:$id--- \r\n<br/>");
        echo $ch->run();
    }
}
