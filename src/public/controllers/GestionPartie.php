<?php

namespace controllers;

use models\Partie as Partie;
use models\Pioche as Pioche;
use models\Joueur as Joueur;
use models\Carte as Carte;

class GestionPartie extends AbstractController{

    public function waitingRoom($request, $response, $args){
        return $this->ci->view->render($response, 'waiting_room.html', [
        ]);    
	}

    public function get_joueurs_max($request, $response, $args){
        session_start();
        $joueurs_max = '';
        if(isset($_SESSION['idpartie'])){
            $id_partie = $_SESSION['idpartie'];
            $joueurs_max = Partie::where('idpartie', $id_partie)
                         ->first()
                         ->joueurs_maximum;
        }
        $response->getBody()->write(json_encode([
            'joueurs_max' => $joueurs_max
        ]));
        return $response;
	}

    public function get_joueurs_actuel($request, $response, $args){
        session_start();
        session_write_close();
        if(isset($_SESSION['idpartie'])){
            $id_partie = $_SESSION['idpartie'];
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

    public function get_mon_tour($request, $response, $args){
        session_start();
        session_write_close();

        while(true){ 
            $mon_tour = false;
            $partie = $this->get_partie()->toarray();
            if($partie['joueur_' . $partie['tour_de']] == $_SESSION['idjoueur']){
                $mon_tour = true;   
            }
            if($mon_tour){  
                $response->getBody()->write(json_encode([
                    'mon_tour' => $mon_tour,
                    'tour_de' => $partie['tour_de']
                ]));
                return $response;
            
            } else {
                sleep(1);
                continue;
            }            
        }
    }

    public function get_mes_cartes($request, $response, $args){
        session_start();
        $cartes = Carte::where('proprietaire', $_SESSION['idjoueur'])
                ->get();
        $response->getBody()->write(json_encode([
            'cartes' => $cartes
        ]));
        return $response;

    }

    public function creer_plateau($request, $response, $args){
        return $this->ci->view->render($response, 'plateau.html', [
        ]);    
    }

    public function pioche_carte($request, $response, $args){
        session_start();
        $carte = -2;
        if(isset($_SESSION['idjoueur']) && isset($_SESSION['idpartie'])){
            
            $pioche = $this->get_pioche();
     
            $pioche_array = $pioche->toarray();
            $idcarte = $pioche_array["carte_" . $pioche_array['haut']];
            $pioche->haut+=1;
            $pioche->save();
            
            $carte = Carte::where('idcarte', $idcarte)
                   ->get()
                   ->first();

            $carte->proprietaire = $_SESSION['idjoueur'];
            $carte->save();

            $this->joueur_suivant();
            $response->getBody()->write(json_encode([
                'pioche' => [
                    'nom' => $carte->nom,
                    'description' => $carte->description,
                    'niveau' => $carte->niveau
                ]
            ]));
            return $response;
        }
    }

    private function get_partie(){
        if(isset($_SESSION['idjoueur']) && isset($_SESSION['idpartie'])){
            $partie = Partie::where('idpartie', $_SESSION['idpartie'])
                    ->first();
            return $partie;
        }
    }

    private function get_pioche(){
        if(isset($_SESSION['idjoueur']) && isset($_SESSION['idpartie'])){
            $idpioche = $this->get_partie()->pioche;
     
            $pioche = Pioche::where('idpioche', $idpioche)
                    ->get()
                    ->first();
            return $pioche;
        }
    }
    
    private function joueur_suivant(){
        $partie = $this->get_partie();
        $tour_de = $partie->tour_de;
        $joueurs_max = $partie->joueurs_maximum;
        if($tour_de >= $joueurs_max){
            $partie->tour_de = 1;
        } else {
            $partie->tour_de = $tour_de + 1;
        }
        $partie->save();
        return $partie->tour_de;
    }
}
