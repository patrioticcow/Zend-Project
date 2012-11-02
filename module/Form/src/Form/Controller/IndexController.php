<?php

namespace Form\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Form\Forms\AddInput;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return array();
    }
    
    public function createAction()
    {
    	$form = new AddInput();

    	return ['form' => $form];
    }
}
