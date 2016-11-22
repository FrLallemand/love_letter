<?php

namespace controllers;

use Illuminate\Database\Capsule\Manager as DB;
use \Interop\Container\ContainerInterface as ContainerInterface;

class AbstractController{
	protected $ci;

	public function __construct(ContainerInterface $ci){
		$this->ci = $ci;
	}
}
