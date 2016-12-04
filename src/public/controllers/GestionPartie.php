<?php

namespace controllers;

use models\Partie as Partie;
use models\Pioche as Pioche;
use models\Joueur as Joueur;
use models\Carte as Carte;

class GestionPartie extends AbstractController{

    public function waitingRoom($request, $response, $args){
        $this->handle_session();
        if($this->check_session()){
            return $this->ci->view->render($response, 'waiting_room.html', [
            ]);
        }
        else{
            $url = $this->ci->router->pathFor('home');
            return $response->withRedirect(($url), 200);
        }
	}

    public function creer_plateau($request, $response, $args){
        $this->handle_session();
        if($this->check_session()){
            return $this->ci->view->render($response, 'plateau.html', [
            ]);
        }
        else{
            $url = $this->ci->router->pathFor('home');
            return $response->withRedirect(($url), 200);
        }
    }

    public function get_joueurs_max($request, $response, $args){
        $this->handle_session();
        $joueurs_max = 0;
        $success = false;
        if($this->check_session()){
            $id_partie = $_SESSION['idpartie'];
            $joueurs_max = Partie::get_partie($id_partie)->joueurs_maximum;
            $success = true;
        }

        $response->getBody()->write(json_encode([
            'success' => $success,
            'joueurs_max' => $joueurs_max
        ]));
        return $response;
	}

    public function get_joueurs_presents($request, $response, $args){
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
                        'joueurs_presents' => $partie->joueurs_presents,
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

    public function attendre($request, $response, $args){
        $this->handle_session();
        session_write_close();
        $dernier_tour =  $args['dernier_tour'];
        $success = false;
        $continue = true;
        $tour_actuel = 0;
        $nom_joueur_actuel = null;
        $mon_tour = false;
        $notification = false;

        if($this->check_session()){
            while($continue){
                $actions = array();
                $partie = Partie::get_partie($_SESSION['idpartie']);
                $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
                $joueur_actuel = $partie->get_joueur_actuel();
                $notification = $joueur->has_notification();
                if($partie->finie && !$notification){
                    $tour_actuel = $partie->tour_actuel;
                    $success = true;
                    $continue = false;
                    $this->handle_session();
                    session_unset();
                    session_write_close();
                    $url = $this->ci->router->pathFor('home');
                    return $response->withRedirect(($url), 200);

                } else {
                    if($partie->is_joueur_actuel($_SESSION['idjoueur'])
                       && $joueur_actuel->elimine == false){
                        $mon_tour = true;
                    }
                    if($mon_tour && !$joueur_actuel->joue){//!$_SESSION['joue']){
                        $joueur_actuel->invulnerable = false;
                        $joueur_actuel->joue = true;//$_SESSION['joue'] = true;

                        //C'est le tour du joueur, il faut l'avertir et lui donner ses actions
                        if(!$joueur_actuel->has_action()){
                            if($partie->tour_actuel <= $partie->joueurs_maximum){
                                $joueur_actuel->add_action('piocher');
                            } else {
                                $joueur_actuel->add_action('jouer');
                                $joueur_actuel->add_action('piocher');
                            }
                        }
                        // $notification = '{"source":"notification_fin",'.
                        //               '"content":{'.
                        //               '}}';
                        // $joueur_actuel->add_notification($notification);
                        // $joueur_actuel->save();
                        $nom_joueur_actuel = $joueur_actuel->nom;
                        $tour_actuel = $partie->tour_actuel;
                        $success = true;
                        $continue = false;
                    }
                    else if($dernier_tour != $partie->tour_actuel){
                        $joueur_actuel->joue = false;//$_SESSION['joue'] = false;
                        $joueur_actuel->save();
                        $nom_joueur_actuel = $joueur_actuel->nom;
                        $tour_actuel = $partie->tour_actuel;
                        $success = true;
                        $continue = false;
                    }
                    else if($notification){
                        $nom_joueur_actuel = $joueur_actuel->nom;
                        $tour_actuel = $partie->tour_actuel;
                        $success = true;
                        $continue = false;
                    }
                    else{
                        sleep(1);
                        $continue = true;
                        continue;
                    }
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'mon_tour' => $mon_tour,
            'tour_actuel' => $tour_actuel,
            'joueur_actuel' => $nom_joueur_actuel,
            'notification' => $notification
        ]));
        return $response;
    }

    public function get_action_suivante($request, $response, $args){
        $this->handle_session();
        $action = null;
        $success = false;
        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur)){
                $success = true;
                if($partie->is_joueur_actuel($joueur->idjoueur)){
                    $actions = $joueur->actions;
                    $action = array_pop($actions);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'action' => $action,
            'success' => $success
        ]));
        return $response;
    }

    public function get_notification_suivante($request, $response, $args){
        $this->handle_session();
        $notification = null;
        $success = false;
        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur)){
                $success = true;
                $notifications = $joueur->notifications;
                $notification = array_pop($notifications);
            }
        }
        $response->getBody()->write(json_encode([
            'notification' => json_decode($notification),
            'success' => $success
        ]));
        return $response;
    }

    public function notification_traitee($request, $response, $args){
        $this->handle_session();
        $success = false;
        $nom_notification = $args['notification'];
        if($this->check_session() && $nom_notification != 'undefined'){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur)){
                $derniere_notification = json_decode($joueur->pop_notification());
                if($derniere_notification->source == $nom_notification){
                    $success = true;
                }
                else {
                    $joueur->add_notification($derniere_notification);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success
        ]));
        return $response;
    }

    public function get_mes_cartes($request, $response, $args){
        $this->handle_session();
        $success = false;
        $cartes = null;
        if($this->check_session()){
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            $cartes = $joueur->get_cartes_non_jouees();
            $success = true;
        }
        $response->getBody()->write(json_encode([
            'mes_cartes' => $cartes,
            'success' => $success
        ]));
        return $response;
    }

    public function get_mes_cartes_jouees($request, $response, $args){
        $this->handle_session();
        $success = false;
        $cartes = null;
        if($this->check_session()){
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            $cartes = $joueur->get_cartes_jouees();
            $success = true;
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'mes_cartes' => $cartes
        ]));
        return $response;
    }

    public function get_cartes_eliminees($request, $response, $args){
        $this->handle_session();
        $success = false;
        $cartes = null;
        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $cartes = Carte::get_cartes_eliminees($partie->pioche);
            $success = true;
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'cartes' => $cartes
        ]));
        return $response;
    }

    public function get_nb_cartes_pioche($request, $response, $args){
        $this->handle_session();
        $cartes = Carte::where('proprietaire', -1)
                ->get()
                ->count();
        $response->getBody()->write(json_encode([
            'nb_cartes_pioche' => $cartes
        ]));
        return $response;
    }

    public function pioche_carte($request, $response, $args){
        $this->handle_session();
        $nom_carte = '';
        $success = false;
        //on est bien dans une session ?
        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            //$joueur_actuel = $partie->get_joueur_actuel();
            //Le joueur de la session est bien le joueur dont c'est le tour ?
            if(isset($partie) && isset($joueur)){ //$partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                //La dernière action est elle bien celle annoncée ?
                if($derniere_action == 'piocher'){
                    // Oui, on l'execute
                    $pioche = $partie->get_pioche();
                    $idcarte = $pioche->piocher();
                    $carte = Carte::get_carte($idcarte);
                    $carte->proprietaire = $joueur->idjoueur;
                    $carte->visible = true;
                    $carte->save();
                    if($carte->nom == 'Comtesse'){
                        $cartes = $joueur->get_cartes_non_jouees();
                        $roi_ou_prince = false;
                        foreach ($cartes as $c){
                            if($c->nom == "Roi" || $c->nom == "Prince"){
                                $roi_ou_prince = true;
                            }
                        }
                        if($roi_ou_prince){
                            $joueur->remove_actions();
                            $joueur->add_action('jouer_comtesse');
                        }
                    }
                    if($carte->nom == 'Prince' || $carte->nom == 'Roi'){
                        $cartes = $joueur->get_cartes_non_jouees();
                        $comtesse = false;
                        foreach ($cartes as $c){
                            if($c->nom == "Comtesse"){
                                $comtesse = true;
                            }
                        }
                        if($comtesse){
                            $joueur->remove_actions();
                            $joueur->add_action('jouer_comtesse');
                        }
                    }
                    $joueur->save();
                    if($joueur->is_tour_fini()){
                        $this->tour_fini();
                    }
                    $success = true;
                }
                else{
                    // Non, on lui redonne
                    $joueur->add_action($derniere_action);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'nom_carte' => $nom_carte
        ]));
        return $response;
    }

    public function jouer_carte($request, $response, $args){
        $this->handle_session();
        $idcarte = $args['idcarte'];
        $success = false;
        //on est bien dans une session ? L'id donné n'est pas undefined et est un nombre ?
        if($this->check_session() && $idcarte != 'undefined' && is_numeric($idcarte)){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            //Le joueur de la session est bien le joueur dont c'est le tour ?
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                //La dernière action est elle bien celle annoncée ?
                if($derniere_action == 'jouer' || $derniere_action == 'jouer_comtesse'){
                    // Oui, on l'execute
                    $carte_jouee = $joueur->get_carte($idcarte);
                    if(isset($carte_jouee)){
                        if($derniere_action == 'jouer'){
                            if($carte_jouee->nom == 'Princesse'){
                                $joueur->elimine = true;
                                $joueur->remove_actions();
                                if($joueur->is_tour_fini()){
                                    $this->tour_fini($joueur_actuel);
                                }

                            }else{
                                $joueur->add_action($carte_jouee->nom);
                            }
                        } else if($derniere_action == 'jouer_comtesse'){
                            if($joueur->is_tour_fini()){
                                $this->tour_fini($joueur_actuel);
                            }
                        }
                        $carte_jouee->played = true;
                        $carte_jouee->ordre = $joueur->get_ordre_derniere_carte_jouee()+1;
                        $carte_jouee->save();
                        $success = true;
                    }
                }
                else{
                    $joueur->add_action($derniere_action);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success
        ]));
        return $response;
    }

    public function get_cartes_jouees_adversaires($request, $response, $args){
        $this->handle_session();
        $success = false;
        $joueurs = array();
        $invulnerable = false;
        $moi = array();
        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur)){
                $liste_joueurs = $partie->joueurs;
                for($i=0; $i<$partie->joueurs_maximum; $i++){
                    $j=Joueur::get_joueur($liste_joueurs[$i]);
                    if($liste_joueurs[$i] != $_SESSION['idjoueur']){
                        $joueurs[] = array(
                            'nom' => $j->nom,
                            'cartes_jouees' => $j->get_cartes_jouees(),
                        );
                        $success = true;
                    }
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'joueurs' => $joueurs
        ]));
        return $response;
    }

    public function get_joueurs($request, $response, $args){
        $this->handle_session();
        $success = false;
        $joueurs = array();
        $invulnerable = false;
        $moi = array();
        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur)){
                $liste_joueurs = $partie->joueurs;
                for($i=0; $i<$partie->joueurs_maximum; $i++){
                    $j=Joueur::get_joueur($liste_joueurs[$i]);
                    if($j->invulnerable || $j->elimine){
                        $invulnerable = true;
                    }
                    if($liste_joueurs[$i] == $_SESSION['idjoueur']){
                        $moi = array(
                            'nom' => $j->nom,
                            'id' => $j->idjoueur,
                            'invulnerable' => $invulnerable
                        );
                    }
                    else {
                        $joueurs[] = array(
                            'nom' => $j->nom,
                            'id' => $j->idjoueur,
                            'invulnerable' => $invulnerable
                        );
                    }
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'joueurs' => $joueurs,
            'moi' => $moi
        ]));
        return $response;
    }

    public function action_garde($request, $response, $args){
        $this->handle_session();
        $id_adversaire = $args['joueur'];
        $nom_carte_choisie = $args['carte'];
        $success = false;
        $message = "Vous n'êtes pas autorisé à effectuer cette action.";
        if($this->check_session() && $id_adversaire != 'undefined' && $nom_carte_choisie != 'undefined' && is_numeric($id_adversaire)){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                if($derniere_action == 'Garde'){
                    $adversaire = Joueur::get_joueur($id_adversaire);
                    if(isset($adversaire) && $partie->is_in_partie($adversaire->idjoueur) && !$adversaire->invulnerable && !$adversaire->elimine){
                        $carte_choisie = Carte::get_carte_nom_proprietaire($nom_carte_choisie, $adversaire->idjoueur);
                        if(isset($carte_choisie)){
                            //La carte est dans la main de l'adversaire, il est éliminé
                            $adversaire->elimine = true;
                            $adversaire->save();
                            $message = "Trouvé !";
                        }
                        else {
                            $message = "Pas de chance...";
                        }
                        if($joueur->is_tour_fini()){
                            $this->tour_fini();
                        }
                        $success = true;
                    }
                }
                else{
                    $joueur->add_action($derniere_action);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $message
        ]));
        return $response;
    }

    public function action_pretre($request, $response, $args){
        $this->handle_session();
        $id_adversaire = $args['joueur'];
        $success = false;
        $message = "Vous n'êtes pas autorisé à effectuer cette action.";
        $cartes = array();
        $message = $id_adversaire;
        if($this->check_session() && $id_adversaire != 'undefined' && is_numeric($id_adversaire)){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                if($derniere_action == 'Prêtre'){
                    $adversaire = Joueur::get_joueur($id_adversaire);
                    if(isset($adversaire) && $partie->is_in_partie($adversaire->idjoueur) && !$adversaire->invulnerable && !$adversaire->elimine){
                        $cartes_adversaire = $adversaire->get_cartes_non_jouees();
                        if(isset($cartes_adversaire)){
                            foreach ($cartes_adversaire as $carte) {
                                $cartes[] = $carte->chemin_image;
                            }
                            if($joueur->is_tour_fini()){
                                $this->tour_fini();
                            }
                            $message = "";
                            $success = true;
                        }
                    }
                }
                else{
                    $joueur->add_action($derniere_action);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'cartes'  => $cartes,
            'message' => $message
        ]));
        return $response;
    }

    public function action_baron($request, $response, $args){
        $this->handle_session();
        $id_adversaire = $args['joueur'];
        $success = false;
        $message = "Vous n'êtes pas autorisé à effectuer cette action.";
        $cartes_adversaire_result = array();
        $cartes_joueur_result = array();
        $niveau_adversaire = 0;
        $niveau_joueur = 0;
        if($this->check_session() && $id_adversaire != 'undefined' && is_numeric($id_adversaire)){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                if($derniere_action == 'Baron'){
                    $adversaire = Joueur::get_joueur($id_adversaire);
                    if(isset($adversaire) && $partie->is_in_partie($adversaire->idjoueur) && !$adversaire->invulnerable && !$adversaire->elimine){
                        $cartes_adversaire = $adversaire->get_cartes();
                        $cartes_joueur = $joueur->get_cartes();
                        foreach ($cartes_adversaire as $carte) {
                            $niveau_adversaire += $carte->niveau;
                            $cartes_adversaire_result[] = $carte->chemin_image;
                        }
                        foreach ($cartes_joueur as $carte) {
                            $niveau_joueur += $carte->niveau;
                            $cartes_joueur_result[] = $carte->chemin_image;
                        }
                        if($niveau_joueur < $niveau_adversaire){
                            $joueur->elimine = true;
                            $message = 'Perdu';
                            $message_adversaire = 'Gagné';
                        } else if($niveau_joueur > $niveau_adversaire){
                            $adversaire->elimine = true;
                            $message = 'Gagné';
                            $message_adversaire = 'Perdu';
                        } else {
                            $message = 'Égalité';
                            $message_adversaire = 'Égalité';
                        }
                        $notification = '{"source":"notification_baron",'.
                                      '"content":{'.
                                      '"joueur_source":"'.$joueur->nom.
                                      '", "cartes_joueur":'.json_encode($cartes_adversaire_result).
                                      ', "niveau_joueur":"'.$niveau_adversaire.
                                      '", "cartes_adversaire":'.json_encode($cartes_joueur_result).
                                      ', "niveau_adversaire":"'.$niveau_joueur.
                                      '", "message":"'.$message_adversaire.
                                      '"}}';
                        $adversaire->add_notification($notification);
                        if($joueur->is_tour_fini()){
                            $this->tour_fini();
                        }
                        $success = true;
                    }
                }
                else{
                    $joueur->add_action($derniere_action);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'cartes_joueur'  => $cartes_joueur_result,
            'niveau_joueur' => $niveau_joueur,
            'cartes_adversaire'  => $cartes_adversaire_result,
            'niveau_adversaire' => $niveau_adversaire,
            'message' => $message
        ]));
        return $response;
    }

    public function action_roi($request, $response, $args){
        $this->handle_session();
        $id_adversaire = $args['joueur'];
        $success = false;
        $message = "Vous n'êtes pas autorisé à effectuer cette action.";
        $cartes_adversaire_result = array();
        $cartes_joueur_result = array();
        $niveau_adversaire = 0;
        $niveau_joueur = 0;
        if($this->check_session() && $id_adversaire != 'undefined' && is_numeric($id_adversaire)){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                if($derniere_action == 'Roi'){
                    $adversaire = Joueur::get_joueur($id_adversaire);
                    if(isset($adversaire) && $partie->is_in_partie($adversaire->idjoueur) && !$adversaire->invulnerable && !$adversaire->elimine){
                        $cartes_adversaire = $adversaire->get_cartes_non_jouees();
                        $cartes_joueur = $joueur->get_cartes_non_jouees();
                        $adversaire->remove_cartes_non_jouees();
                        $joueur->remove_cartes_non_jouees();

                        $joueur->add_cartes($cartes_adversaire);
                        $adversaire->add_cartes($cartes_joueur);

                        $notification = '{"source":"notification_roi",'.
                                      '"content":{'.
                                      '"joueur_source":"'.$joueur->nom.
                                      '"}}';
                        $adversaire->add_notification($notification);
                        if($joueur->is_tour_fini()){
                            $this->tour_fini();
                        }
                        $message = "";
                        $success = true;
                    }
                }
                else{
                    $joueur->add_action($derniere_action);
                }

            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $message
        ]));
        return $response;
    }

    public function action_prince($request, $response, $args){
        $this->handle_session();
        $id_adversaire = $args['joueur'];
        $success = false;
        $message = "Vous n'êtes pas autorisé à effectuer cette action.";
        if($this->check_session() && $id_adversaire != 'undefined' && is_numeric($id_adversaire)){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                if($derniere_action == 'Prince'){
                    $adversaire = Joueur::get_joueur($id_adversaire);
                    if(isset($adversaire) && $partie->is_in_partie($adversaire->idjoueur) && !$adversaire->invulnerable && !$adversaire->elimine){
                        $notification = '{"source":"notification_prince",'.
                                      '"content":{'.
                                      '"joueur_source":"'.$joueur->nom.
                                      '"}}';
                        $adversaire->add_notification($notification);
                        $adversaire->remove_cartes_non_jouees();
                        $pioche = $partie->get_pioche();
                        $idcarte = $pioche->piocher();
                        $carte = Carte::get_carte($idcarte);
                        $carte->proprietaire = $adversaire->idjoueur;
                        $carte->visible = true;
                        $carte->save();
                        $message = "";
                        if($joueur->is_tour_fini()){
                            $this->tour_fini();
                        }
                        $success = true;
                    }
                }
                else{
                    $joueur->add_action($derniere_action);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $message
        ]));
        return $response;
    }

    public function action_servante($request, $response, $args){
        $this->handle_session();
        $success = false;
        $message = "";
        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $derniere_action = $joueur->pop_action();
                if($derniere_action == 'Servante'){
                    if($joueur->is_tour_fini()){
                        $this->tour_fini();
                    }
                    $joueur->invulnerable = true;
                    $joueur->save();
                    $success = true;
                }
                else{
                    $joueur->add_action($derniere_action);
                }
            }
        }
        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $message
        ]));
        return $response;
    }

    private function tour_fini(){
        $this->handle_session();

        if($this->check_session()){
            $partie = Partie::get_partie($_SESSION['idpartie']);
            $joueur = Joueur::get_joueur($_SESSION['idjoueur']);
            $pioche = $partie->get_pioche();
            if(isset($partie) && isset($joueur) && $partie->is_joueur_actuel($joueur->idjoueur)){
                $joueurs_maximum = $partie->joueurs_maximum;
                $joueurs = $partie->joueurs;
                $index_du_joueur_actuel = array_search($joueur->idjoueur, $joueurs);
                $found = false;
                $continue = true;
                $index_joueur_suivant = $index_du_joueur_actuel;
                while($continue && !$found){
                    if($index_joueur_suivant+1 == $joueurs_maximum){
                        $index_joueur_suivant = 0;
                    } else {
                        $index_joueur_suivant += 1;
                    }
                    if($index_joueur_suivant != $index_du_joueur_actuel){
                        $joueur_suivant = Joueur::get_joueur($joueurs[$index_joueur_suivant]);
                        if(!$joueur_suivant->elimine){
                            $continue = false;
                            $found = true;
                        }
                    }
                    else {
                        $continue = false;
                        $found = false;
                    }
                }
                if($found){
                    $partie->joueur_actuel = $joueurs[$index_joueur_suivant];
                    $joueur->joue = false;
                }
                if(!$found || $pioche->haut>$pioche->cartes_max) {//partie finie
                    $joueur->joue = false;
                    //$partie->joueur_actuel = -1;
                    $partie->finie = true;
                    $v = $partie->get_vainqueur();

                    $notification = '{"source":"notification_fin",'.
                                  '"content":{'.
                                  '"vainqueur":"'.$v.
                                  '"}}';

                    $joueurs = $partie->get_joueurs();
                    foreach($joueurs as $j){
                        $j->add_notification($notification);
                    }
                }
                $joueur->save();
                $partie->tour_actuel += 1;
                $partie->save();
            }
        }
    }
}
