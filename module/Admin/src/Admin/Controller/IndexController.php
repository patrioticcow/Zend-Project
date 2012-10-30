<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
	
    public function usersAction()
    {
    	//$sm = $this->getServiceLocator()->get('users_mapper');
    	//var_dump($sm);
    	return new ViewModel();
    }

}
