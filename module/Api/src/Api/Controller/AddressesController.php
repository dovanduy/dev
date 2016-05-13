<?php

namespace Api\Controller;

class AddressesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Addresses\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Addresses'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Addresses\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Addresses'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Addresses\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Addresses'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Addresses\All::getInstance()->execute(
            $this->getServiceLocator()->get('Addresses'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Addresses\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Addresses'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Addresses\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Addresses'),
            $this->getParams()
        );
    }
    
}
