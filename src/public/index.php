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


$app->get('/', '\Home:homeScreen')->setName('home');

$app->get('/creer_partie', '\CreerPartie:formCreerPartie') -> setName('creer_partie');
$app->post('/creer_partie/{joueurs_maximum}+{nom_joueur}','\CreerPartie:creerPartie');

$app->get('/partie/attente', '\GestionPartie:waitingRoom');
$app->post('/partie/attente', '\GestionPartie:waitingRoom');

$app->get('/partie/plateau', '\GestionPartie:creer_plateau')->setName('plateau');
$app->get('/partie/pioche_carte', '\GestionPartie:pioche_carte');

$app->post('/partie/jouer_carte/{idcarte}', '\GestionPartie:jouer_carte');

$app->post('/partie/action_garde/{joueur}+{carte}', '\GestionPartie:action_garde');
$app->post('/partie/action_pretre/{joueur}', '\GestionPartie:action_pretre');
$app->post('/partie/action_baron/{joueur}', '\GestionPartie:action_baron');
$app->post('/partie/action_servante', '\GestionPartie:action_servante');
$app->post('/partie/action_roi/{joueur}', '\GestionPartie:action_roi');
$app->post('/partie/action_prince/{joueur}', '\GestionPartie:action_prince');
$app->post('/partie/confirmer_notification/{notification}', '\GestionPartie:notification_traitee');

$app->get('/partie/joueurs_max', '\GestionPartie:get_joueurs_max');
$app->get('/partie/joueurs_presents/{timestamp}', '\GestionPartie:get_joueurs_presents');
//$app->get('/partie/tour_de', '\GestionPartie:get_tour_de');

$app->get('/partie/mon_tour', '\GestionPartie:get_mon_tour');
$app->get('/partie/attendre/{dernier_tour}', '\GestionPartie:attendre');

$app->get('/partie/get_action_suivante', '\GestionPartie:get_action_suivante');
$app->get('/partie/get_notification_suivante', '\GestionPartie:get_notification_suivante');

$app->get('/partie/mes_cartes', '\GestionPartie:get_mes_cartes');
$app->get('/partie/cartes_jouees', '\GestionPartie:get_mes_cartes_jouees');
$app->get('/partie/cartes_jouees_adversaires', '\GestionPartie:get_cartes_jouees_adversaires');
$app->get('/partie/cartes_eliminees', '\GestionPartie:get_cartes_eliminees');

$app->get('/partie/get_joueurs', '\GestionPartie:get_joueurs');

$app->get('/joueur/get_partie', '\GestionJoueur:get_partie');

$app->run();
