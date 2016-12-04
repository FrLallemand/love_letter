<?php

namespace models;

class Joueur extends \Illuminate\Database\Eloquent\Model{
	protected $table = "joueur";
	protected $primaryKey = "idjoueur";
    protected $casts = [
        'actions' => 'array',
        'notifications' => 'array'
    ];
	public $timestamps = false;


    /*
      Ajoute une action au joueur
    */
    public function add_action($action){
        $actions = $this->actions;
        $actions[] =$action;
        $this->actions = $actions;
        $this->save();
    }

    public function remove_cartes(){
        $cartes = Carte::where('proprietaire', $this->idjoueur)
                ->get();

        foreach($cartes as $carte){
            $carte->played = true;
            $carte->visible = true;
        }
        $cartes->save();
    }

    public function remove_cartes_non_jouees(){
        $cartes = Carte::where('proprietaire', $this->idjoueur)
                ->where('played', false)
                ->where('visible', true)
                ->get();

        foreach($cartes as $carte){
            $carte->proprietaire = -2;
            $carte->save();
        }
    }

    public function add_cartes($cartes){
        foreach($cartes as $carte){
            $carte->proprietaire = $this->idjoueur;
            $carte->save();
        }
    }

    public function get_cartes(){
        $cartes = Carte::where('proprietaire', $this->idjoueur)
                ->get();
        return $cartes;
    }

    public function get_cartes_non_jouees(){
        $cartes = Carte::where('proprietaire', $this->idjoueur)
                ->where('played', false)
                ->where('visible', true)
                ->get();
        return $cartes;
    }

    public function get_cartes_jouees(){
        $cartes = Carte::where('proprietaire', $this->idjoueur)
                ->where('played', true)
                ->where('visible', true)
                ->orderBy('ordre')
                ->get();
        return $cartes;
    }

    public function get_ordre_derniere_carte_jouee(){
        $carte = Carte::where('proprietaire', $this->idjoueur)
               ->where('played', true)
               ->where('visible', true)
               ->max('ordre');
        return $carte;
    }

    public function get_carte($idcarte){
        $e = $this->idjoueur;
        $carte = Carte::get_carte_proprietaire($idcarte, $this->idjoueur);
        return $carte;
    }


    /*
      Enleve la derniere action du joueur actuel et la retourne
    */
    public function pop_action(){
        $actions = $this->actions;
        $action = array_pop($actions);
        $this->actions = $actions;
        $this->save();
        return $action;
    }

    /*
      Enleve les actions du joueur
    */
    public function remove_actions(){
        $actions = array();
        $this->actions = $actions;
        $this->save();
    }

    /*
      Ajoute une notification au joueur
    */
    public function add_notification($notification){
        $notifications = $this->notifications;
        $notifications[] = $notification;
        $this->notifications = $notifications;
        $this->save();
    }

    /*
      anciennement get_notification_existante
     */
    public function has_notification(){
        $resultat = false;
        if($this->notifications != null){
            $resultat = true;
        }
        return $resultat;
    }

    public function has_action(){
        $resultat = false;
        if($this->actions != null){
            $resultat = true;
        }
        return $resultat;
    }


    /*
      Enleve la derniere notification du joueur actuel et la retourne
    */
    public function pop_notification(){
        $notifications = $this->notifications;
        $notification = array_shift($notifications);
        $this->notifications = $notifications;
        $this->save();
        return $notification;
    }


    public function is_tour_fini(){
        $resultat = false;
        if($this->actions == null){
            $resultat = true;
        }
        return $resultat;
    }

    /*
      Retourne le joueur avec l'id $idjoueur
    */
    public static function get_joueur($idjoueur){
        $joueur = Joueur::where('idjoueur', $idjoueur)
                ->first();
        return $joueur;
    }

}