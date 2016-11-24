<?php

namespace controllers;

use models\Partie as Partie;
use models\Joueur as Joueur;

class GestionPartie extends AbstractController{

    public function get_partie($request, $response, $args){
        session_start();
        $id_partie = '';

        // Le joueur a déjà une session ?
        if(isset($_SESSION['idjoueur'])){
            // Oui, a-t-il une partie ?            
            if(isset($_SESSION['idpartie'])){
                $id_partie = $_SESSION['idpartie'];
            }
        }        
        $response->getBody()->write(json_encode([
            'id_partie' => $id_partie
        ]));
        return $response;
    }
}
