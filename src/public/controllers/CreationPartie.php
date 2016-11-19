<?php

namespace controllers;

class CreationPartie extends AbstractController{

	public function formCreerPartie($request, $response, $args){
		return $this->ci->view->render($response, 'creer_partie.html', [
        ]);
	}

    public function creerPartie($request, $response, $args){
        return $this->ci->view->render($response, 'test.html', [
            'name' => $request->getParam('user')
        ]);
	}

}
