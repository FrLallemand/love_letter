<?php

namespace models;
use models\Partie as Partie;
use models\Pioche as Pioche;
use models\Joueur as Joueur;
use models\Carte as Carte;

class Partie extends \Illuminate\Database\Eloquent\Model{
	protected $table = "partie";
	protected $primaryKey = "idpartie";
    protected $casts = [
        'joueurs' => 'array'
    ];
	public $timestamps = true;

    /*
      Retourne le joueur actuel de la partie
    */
    public function get_joueur_actuel(){
        $joueur_actuel = Joueur::join('partie', 'joueur.idjoueur', 'partie.joueur_actuel')
                       ->whereRaw('joueur.idjoueur = partie.joueur_actuel')
                       ->where('partie.idpartie', $this->idpartie)
                       ->first();
        return $joueur_actuel;
    }

    public function is_joueur_actuel($idjoueur){
        $resultat = false;
        $joueur_actuel = $this->get_joueur_actuel();
        if($joueur_actuel->idjoueur == $idjoueur){
                $resultat = true;
        }
        return $resultat;
    }

    /*
      Retourne la pioche de la partie
    */
    public function get_pioche(){
        $pioche = Pioche::get_pioche($this->pioche);
        return $pioche;
    }

    public function is_in_partie($idjoueur){
        $resultat = false;
        for($i=0; $i<$this->joueurs_maximum; $i++){
            if($this->joueurs[$i] == $idjoueur){
                $resultat = true;
            }
        }
        return $resultat;
    }

    public function get_joueurs(){
        $joueurs = array();
        for($i=0; $i<$this->joueurs_maximum; $i++){
            $joueurs[] = Joueur::get_joueur($this->joueurs[$i]);
        }
        return $joueurs;
    }

    public function get_vainqueur(){
        $max = 0;
        $vainqueur = null;
        for($i=0; $i<$this->joueurs_maximum; $i++){
            $score = 0;
            $joueur = Joueur::get_joueur($this->joueurs[$i]);
            $cartes = $joueur->get_cartes_non_jouees();
            foreach($cartes as $c){
                $score += $c->niveau;
            }
            if($score > $max){
                $max = $score;
                $vainqueur = $joueur->nom;
            }
        }
        return $vainqueur;
    }

    /*
      Retourne la partie avec l'id $idpartie
    */
    public static function get_partie($idpartie){
        $partie = Partie::where('idpartie', $idpartie)
                ->first();
        return $partie;
    }

}
