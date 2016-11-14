<?php
require '../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$db = new \Illuminate\Database\Capsule\Manager;
$db->addConnection(parse_ini_file('../..//conf/database.conf.ini'));

$db->setAsGlobal();
$db->bootEloquent();

$app = new \Slim\App;
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Plopitiplop");

    return $response;
});
$app->run();