<?php
$db = new \Illuminate\Database\Capsule\Manager;
$db->addConnection(parse_ini_file('../../conf/database.conf.ini'));

$db->setAsGlobal();
$db->bootEloquent();

date_default_timezone_set('Europe/Paris');

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

$container['\Home'] = function ($c) {
 	return new \controllers\Home($c);
};

$container['\CreerPartie'] = function ($c) {
 	return new \controllers\CreationPartie($c);
};
$container['\GestionPartie'] = function ($c) {
 	return new \controllers\GestionPartie($c);
};

