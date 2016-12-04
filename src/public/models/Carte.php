<?php

namespace models;

class Carte extends \Illuminate\Database\Eloquent\Model{
	protected $table = "carte";
	protected $primaryKey = "idcarte";
	public $timestamps = false;

    public function setCarte($prop, $nom, $desc, $niveau, $image){
        $this->proprietaire = $prop;
        $this->nom = $nom;
        $this->description = $desc;
        $this->niveau = $niveau;
        $this->chemin_image = $image;
        $this->save();
    }

    /*
      Retourne la carte avec l'id $idcarte
     */
    public static function get_carte($idcarte){
        $carte = Carte::where('idcarte', $idcarte)
               ->first();
        return $carte;
    }

    public static function get_cartes_eliminees($idpioche){
        $cartes = Carte::where('pioche', $idpioche)
                ->where('proprietaire', -2)
                ->where('visible', true)
                ->get();
        return $cartes;
    }

    public static function get_carte_proprietaire($idcarte, $prop){
        $carte = Carte::where('idcarte', $idcarte)
               ->where('proprietaire', $prop)
               ->get()
               ->first();
        return $carte;
    }

    public static function get_carte_nom_proprietaire($nom, $prop){
        $carte = Carte::where('nom', $nom)
               ->where('proprietaire', $prop)
               ->where('played', false)
               ->get()
               ->first();
        return $carte;
    }

}
