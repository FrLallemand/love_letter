<?php

namespace controllers;
use Carbon\Carbon;
use models\Partie as Partie;
use models\Joueur as Joueur;

class CreationPartie extends AbstractController{

	public function formCreerPartie($request, $response, $args){
		return $this->ci->view->render($response, 'creer_partie.html', [
        ]);
	}

    public function creerPartie($request, $response, $args){
        date_default_timezone_set('Europe/Paris');

        $joueurs_maximum = $args['joueurs_maximum'];
        $nom_joueur =  $args['nom_joueur'];

        //on cherche si il y a une partie en attente de joueurs
        $partie_attente = Partie::where('joueurs_maximum', $joueurs_maximum)
                        ->whereRaw('joueurs_actuel < joueurs_maximum')
                        ->get()
                        ->first();

        $id_partie = -1;
        if($partie_attente == NULL){
            // Il n'y en a pas, on crée une nouvelle partie
            $partie = new Partie();
            $partie->joueurs_maximum = $joueurs_maximum;
            $partie->save();
        }else{
            $partie = $partie_attente;
        }
        $id_partie = $partie->idpartie;

        // on regarde si le joueur peut prendre ce nom
        // Pour le moment, la vérification se fait sur les joueurs de la partie
        // TODO idjoueur doit être l'id de session, pour que le joueur ne puisse pas jouer à deux parties en même temps
        session_start();

        $status_nom = '';
        if(Joueur::where('nom', $nom_joueur)
           ->where('idpartie', $id_partie)
           ->exists()){
            $status_nom = 'Nom d\'utilisateur indisponible';
        } elseif(isset($_SESSION['idjoueur']) && Joueur::where('idjoueur', $_SESSION['idjoueur'])->exists()){
            $status_nom = 'Le joueur est déjà dans une partie';
        } else {

            //Le nom est libre
            $joueur = new Joueur();
            $joueur->idpartie = $id_partie;
            $joueur->nom = $nom_joueur;
            $joueur->save();

            //On enregistre l'id du joueur dans la session de l'utilisateur
            $_SESSION['idjoueur'] = $joueur->idjoueur;


            $partie->joueurs_actuel++;
            switch($partie->joueurs_actuel){
            case 1:
                $partie->joueur_1 = $joueur->idjoueur;
                break;
            case 2:
                $partie->joueur_2 = $joueur->idjoueur;
                break;
            case 3:
                $partie->joueur_3 = $joueur->idjoueur;
                break;
            case 4:
                $partie->joueur_4 = $joueur->idjoueur;
                break;
            }
            $partie->save();
            $status_nom = '';
        }

        $response->getBody()->write(json_encode([
            'id_partie' => $id_partie,
            'status_nom' =>$status_nom
        ]));
        return $response;
    }
}
