<?php

namespace controllers;

use Illuminate\Database\Capsule\Manager as DB;
use \Interop\Container\ContainerInterface as ContainerInterface;

class AbstractController{
	protected $ci;

	public function __construct(ContainerInterface $ci){
		$this->ci = $ci;
	}

    protected function handle_session(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function check_session(){
        $result = false;
        if(isset($_SESSION['idjoueur']) && isset($_SESSION['idpartie'])){
            $result = true;
        }
        return $result;
    }
}
