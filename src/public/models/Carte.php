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
}
