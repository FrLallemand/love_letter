<?php

namespace controllers;

use Illuminate\Database\Capsule\Manager as DB;

class AbstractController{
	public $request;

	public function __construct($req){
		$this->request = $req;
	}
}

?>