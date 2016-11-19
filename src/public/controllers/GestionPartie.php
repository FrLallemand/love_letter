<?php

namespace controllers;

class GestionPartie extends AbstractController{


    public function creerPartie($request, $response, $args){
        return $this->view->render($response, 'test.html', [
            'name' => $request->getParam('user')
        ]);    
	}

}
