<?php

namespace models;

class Carte extends \Illuminate\Database\Eloquent\Model{
	protected $table = "carte";
	protected $primaryKey = "idcarte";
	public $timestamps = false;

    public function setCarte($prop, $nom, $desc, $niveau){
        $this->proprietaire = $prop;
        $this->nom = $nom;
        $this->description = $desc;
        $this->niveau = $niveau;
        $this->save();
    }
}
