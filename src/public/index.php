<?php
require '../vendor/autoload.php';
require 'settings.php';

use \Interop\Container\ContainerInterface as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use controllers\Home;
use controllers\CreationPartie;
use controllers\GestionPartie;;
use controllers\GestionJoueur;;


$app->get('/', '\Home:homeScreen');

$app->get('/creer_partie', '\CreerPartie:formCreerPartie') -> setName('creer_partie');
$app->post('/creer_partie/{joueurs_maximum}+{nom_joueur}','\CreerPartie:creerPartie');

$app->get('/test', function (Request $request, Response $response) {
    $response->getBody()->write("Plopitiplop");

    return $response;
});

$app->get('/partie/attente', '\GestionPartie:waitingRoom');
$app->post('/partie/attente', '\GestionPartie:waitingRoom');

$app->get('/partie/plateau', '\GestionPartie:creer_plateau');
$app->get('/partie/pioche_carte', '\GestionPartie:pioche_carte');
$app->get('/partie/joueurs_max', '\GestionPartie:get_joueurs_max');
$app->get('/partie/joueurs_actuel/{timestamp}', '\GestionPartie:get_joueurs_actuel');
//$app->get('/partie/tour_de', '\GestionPartie:get_tour_de');
$app->get('/partie/mon_tour', '\GestionPartie:get_mon_tour');
$app->get('/partie/mes_cartes', '\GestionPartie:get_mes_cartes');

$app->get('/joueur/get_partie', '\GestionJoueur:get_partie');

$app->run();
