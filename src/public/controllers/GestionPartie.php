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
        while(true){
            $partie = Partie::where('idpartie', $id_partie)
                    ->first();

            $last_ajax_call =  null;
            if($args['timestamp'] != 'undefined'){
                $last_ajax_call =  strtotime($args['timestamp']);
            }
                        
            $last_change_in_data = strtotime($partie->updated_at->format('y-m-d h:i:s'));

            if ($last_ajax_call == null || $last_change_in_data > $last_ajax_call) {
                $response->getBody()->write(json_encode([
                    'joueurs_actuel' => $partie->joueurs_actuel,
                    'timestamp' => $partie->updated_at->format('y-m-d h:i:s')
                ]));
                return $response;
            }else{
                sleep(1);
                continue;
            }
        }
	}
}
