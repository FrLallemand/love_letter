<?php

require '../vendor/autoload.php';
require 'settings.php';

use \Interop\Container\ContainerInterface as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use controllers\Home;
use controllers\CreationPartie;
use controllers\GestionPartie;;


$app->get('/', '\Home:homeScreen');

$app->get('/creer_partie', '\CreerPartie:formCreerPartie') -> setName('creer_partie');
$app->post('/creer_partie','\CreerPartie:creerPartie');

$app->get('/test', function (Request $request, Response $response) {
    $response->getBody()->write("Plopitiplop");

    return $response;
});

//TODO
$app->get('/partie/{idpartie}', '\GestionPartie:') -> setName('');
$app->post('/partie/{idpartie}', '\GestionPartie:');

$app->run();