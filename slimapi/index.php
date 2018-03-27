<?php

require './vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

define('BASEPATH', __DIR__ . DIRECTORY_SEPARATOR);

require_once BASEPATH . 'helper/helper.php';
require_once BASEPATH . 'config/config.php';

$app = new Slim\App($api_config);

$files = readdir_phpfile(BASEPATH . APIPATH);
foreach ($files as $key => $filename) {
    is_file($filename) ? require_once $filename : "";
}

$app->run();
