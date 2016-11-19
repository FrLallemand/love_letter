<?php

namespace controllers;

use models\Partie as Partie;
use models\Joueur as Joueur;
    
class CreationPartie extends AbstractController{

	public function formCreerPartie($request, $response, $args){
		return $this->ci->view->render($response, 'creer_partie.html', [
        ]);
	}

    public function creerPartie($request, $response, $args){
        $joueurs_maximum = $request->getParam('joueurs_maximum');
        $nom_joueur =  $request->getParam('nom_joueur');

        //on regarde si le joueur peut prendre ce nom
        // Pour le moment, la vérification se fait sur l'ensemble des joueurs
        // Plus tard, peut être le faire uniquement sur la partie ?
        
        if(Joueur::where('nom', $nom_joueur)->exists()){
            $status = 'Nom d\'utilisateur indisponible';
        }else{
            //Le nom est libre
            $joueur = new Joueur();
            $joueur->nom = $nom_joueur;
            $joueur->save();
            $status = 'ok';
        }
        
        $response->getBody()->write(json_encode(['response' => $status]));
        return $response;
        
        //on cherche si il y a une partie en attente de joueurs
        // $partie_attente = Partie::where('joueurs_maximum', $joueurs_maximum)
        //                 ->where('started', false)->get()->first();



        
        // if(empty($partie_attente)){
        //     // Il n'y en a pas, on crée une nouvelle partie
        //     $partie = new Partie();
        //     $partie->joueurs_maximum = $joueurs_maximum;
        //     $partie->save();

        // }else{
        //     echo($partie_attente);
        // }

        //echo($partie_attente);
        // return $this->ci->view->render($response, 'test.html', [
        //     'name' => $partie_attente
        //  ]);
    }
}
