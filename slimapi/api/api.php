<?php
require __DIR__ . '/../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
 

define('_ROOT_', __DIR__);
//CACHE***********************
$container = new \Slim\Container;
$app = new \Slim\App($container);
$c = $app->getContainer();
//$app->add(new \Slim\HttpCache\Cache('public', 36000));


$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $debug = $exception->getMessage() . ' file:' . $exception->getFile() . ' line:' . $exception->getLine();
        return $container['response']->withStatus(500)
                        ->withHeader('Content-Type', 'text/html')
                        ->write('Something went wrong!' . ($debug));
    };
};
//Product Search **************************************************************
$app->auth = function ($request, $response, $next) use ($app) {
    $input = $request->getParsedBody();
    $token = empty($input['token']) ? 0 : trim($input['token']);  
    if ($token == 1) {
        $response = $next($request, $response);
        return $response;
    } else { 
        $newResponse = $response->withJson(['error']);
        return $newResponse;
    }
};
//slim application routes  
$app->get('/', function ($request, $response, $args) { 
    $response->write(json_encode([]));
    return $response;
})->add($app->auth);
 
