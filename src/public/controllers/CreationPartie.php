<?php

namespace controllers;
use Carbon\Carbon;
use models\Partie as Partie;
use models\Pioche as Pioche;
use models\Joueur as Joueur;
use models\Carte as Carte;

class CreationPartie extends AbstractController{

	public function formCreerPartie($request, $response, $args){
		return $this->ci->view->render($response, 'creer_partie.html', [
        ]);
	}

    public function creerPartie($request, $response, $args){
        session_start();

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
            $this->creerCartes($partie->idpartie);
        }else{
            $partie = $partie_attente;
        }
        $id_partie = $partie->idpartie;

        // on regarde si le joueur peut prendre ce nom
        // Pour le moment, la vérification se fait sur les joueurs de la partie
        $status_nom = '';
        if(Joueur::where('nom', $nom_joueur)
           ->where('idpartie', $id_partie)
           ->exists()){
            $status_nom = 'Nom indisponible';
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
            $_SESSION['idpartie'] = $id_partie;
                                  
            $partie->joueurs_actuel++;
            switch($partie->joueurs_actuel){
            case 1:
                $partie->joueur_1 = $joueur->idjoueur;
                $partie->tour_de = $joueur->idjoueur;
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
            'status_nom' =>$status_nom
        ]));
        return $response;
    }

    private function creerCartes($idpartie){
        $cartes = [];
        for($i = 0; $i < 16; $i++){
            if($i<5){
                $c = new Carte();
                $c->setCarte(-1, "Garde", "Je suis un garde.", 1);
            }
            elseif($i<7){
                $c = new Carte();
                $c->setCarte(-1, "Prêtre", "Je suis un prêtre.", 2);
            }
            elseif($i<9){
                $c = new Carte();
                $c->setCarte(-1, "Baron", "Je suis un baron.", 3);
            }
            elseif($i<11){
                $c = new Carte();
                $c->setCarte(-1, "Servante", "Je suis une servante.", 4);
            }
            elseif($i<13){
                $c = new Carte();
                $c->setCarte(-1, "Prince", "Je suis un prince.", 5);
            }
            elseif($i==13){
                $c = new Carte();
                $c->setCarte(-1, "King", "Je suis le roi.", 6);
            }
            elseif($i==14){
                $c = new Carte();
                $c->setCarte(-1, "Comtesse", "Je suis la comtesse.", 7);
            }
            elseif($i==15){
                $c = new Carte();
                $c->setCarte(-1, "Princesse", "Je suis la princesse.", 8);
            }
            $cartes[$i] = $c->idcarte;
        }
        shuffle($cartes);
        $pioche = new Pioche();
        $pioche->idpartie = $idpartie;
        $pioche->setPioche($cartes);
        return $cartes;
    }
}
