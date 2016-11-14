<?php
require '../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$db = new \Illuminate\Database\Capsule\Manager;
$db->addConnection(parse_ini_file('../../conf/database.conf.ini'));

$db->setAsGlobal();
$db->bootEloquent();

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);

$app = new \Slim\App($c);

$container = $app->getContainer();

$container['view'] = function($container){
	$view = new \Slim\Views\Twig('../../templates', ['cache' => false]);

	$basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
	$view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

	return $view;
};

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("plop");

    return $response;
});

$app->get('/chemin/{name}', function($request, $response, $args){
	return $this->view->render($response, 'test.html', [
		'name' => $args['name']
	]);
});

$app->run();