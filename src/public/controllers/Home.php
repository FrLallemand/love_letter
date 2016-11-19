<?php

namespace controllers;

class Home extends AbstractController{

	public function homeScreen($request, $response, $args){
		return $this->ci->view->render($response, 'home.html', [
        ]);
	}
}
