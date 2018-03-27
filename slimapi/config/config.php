<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$api_config = [
    'settings' => [
        'displayErrorDetails' => true,
        'logger' => [
            'name' => 'slim-app',
//            'level' => Monolog\Logger::DEBUG,
            'path' => BASEPATH . 'logs/app.log',
        ],
    ],
];

/* Api root */
define('APIPATH', 'api');
/* Api root */
define('PUBLICPATH', 'assests');
/* Api root */
define('DEFAULT_CHARSET', 'utf-8');


$autoload = ['config', 'lib', 'helper'];
$autoload_file = [
    'lib/CNMarc/loader.php'
]; 

foreach ($autoload as $dir) {
    $files = readdir_phpfile(BASEPATH . $dir);
    foreach ($files as $key => $filename) {
        is_file($filename) ? require_once $filename : "";
    }
}
foreach ($autoload_file as $key => $filename) {
    is_file(BASEPATH . $filename) ? require_once BASEPATH . $filename : "";
}