<?php

namespace Form\Controller;

use Zend\Mvc\Controller\AbstractActionController, Zend\View\Model\JsonModel, Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class IndexController extends AbstractActionController
{
	public function indexAction() {
		return array ();
	}
	
	public function createAction() {
		$headScript = new PhpRenderer ();
		$headScript->headScript ()->appendFile ( '/js/form/create.form.js' );
		$headScript->headScript ()->appendFile ( '/js/form/line.text.js' );
		$headScript->headScript ()->appendFile ( '/js/form/add.form.js' );
		
		$form = '';
		
		return [ 
				'form' => $form 
		];
	}
	
	public function inputAction() {
		$request = $this->getRequest ();
		$results = $request->getQuery ();
		
		$result = new ViewModel ([ 
				'result' => $results,
		]);
		
		$result->setTerminal ( true );
		
		return $result;
	}
	
	public function numberAction() {
		$result = new JsonModel ( array (
				'some_parameter' => 'some value',
				'success' => true 
		) );
		
		return $result;
	}
}
