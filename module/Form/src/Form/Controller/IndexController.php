<?php

namespace Form\Controller;

use Zend\Mvc\Controller\AbstractActionController,
	Form\Forms\AddInput,
	Zend\View\Model\JsonModel,
	Zend\View\Model\ViewModel;

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
    
    public function inputAction()
    {
    	$request = $this->getRequest();
    	$results = $request->getQuery();
    	
    	$form = new AddInput();
    	
    	$result = new ViewModel(['result' => $results, 'form' => $form]);
    	
    	$result->setTerminal(true);
    	
        return $result;
    }
    
    public function numberAction()
    {
    	$result = new JsonModel(array(
        	'some_parameter' => 'some value',
            'success'=>true,
        ));

        return $result;
    }
}
