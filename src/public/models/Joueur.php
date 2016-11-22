<?php

namespace models;

class Joueur extends \Illuminate\Database\Eloquent\Model{
	protected $table = "joueur";
	protected $primaryKey = "idjoueur";
	public $timestamps = false;
}