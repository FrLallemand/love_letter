<?php

namespace controllers;

use models\Partie as Partie;
use models\Joueur as Joueur;

class GestionPartie extends AbstractController{

    public function waitingRoom($request, $response, $args){
        return $this->ci->view->render($response, 'waiting_room.html', [
        ]);    
	}

    public function get_joueurs_max($request, $response, $args){
        $id_partie = $args['idpartie'];
        $joueurs_max = Partie::where('idpartie', $id_partie)
                     ->first()
                     ->joueurs_maximum;
        $response->getBody()->write(json_encode([
            'joueurs_max' => $joueurs_max,
        ]));
        return $response;
	}

    public function get_joueurs_actuel($request, $response, $args){
        $id_partie = $args['idpartie'];
        $joueurs_actuel = Partie::where('idpartie', $id_partie)
                     ->first()
                     ->joueurs_actuel;

        $response->getBody()->write(json_encode([
            'joueurs_actuel' => $joueurs_actuel            
        ]));
        return $response;
	}

}
