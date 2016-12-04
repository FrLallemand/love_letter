<?php

namespace models;

class Pioche extends \Illuminate\Database\Eloquent\Model{
	protected $table = "pioche";
	protected $primaryKey = "idpioche";
	public $timestamps = false;

    public function setPioche($cartes){
        $this->carte_1 = $cartes[0];
        $this->carte_2 = $cartes[1];
        $this->carte_3 = $cartes[2];
        $this->carte_4 = $cartes[3];
        $this->carte_5 = $cartes[4];
        $this->carte_6 = $cartes[5];
        $this->carte_7 = $cartes[6];
        $this->carte_8 = $cartes[7];
        $this->carte_9 = $cartes[8];
        $this->carte_10 = $cartes[9];
        $this->carte_11 = $cartes[10];
        $this->carte_12 = $cartes[11];
        $this->carte_13 = $cartes[12];
        $this->carte_14 = $cartes[13];
        $this->carte_15 = $cartes[14];
        $this->carte_16 = $cartes[15];

        $this->save();
    }

    public function piocher(){
        $pioche_array = $this->toarray();
        $idcarte = $pioche_array["carte_" . $pioche_array['haut']];
        $this->haut+=1;
        $this->save();
        return $idcarte;
    }

    /*
      Retourne la pioche avec l'id $idpioche
    */
    public static function get_pioche($idpioche){
        $pioche = Pioche::where('idpioche', $idpioche)
                ->get()
                ->first();
        return $pioche;
    }
}
