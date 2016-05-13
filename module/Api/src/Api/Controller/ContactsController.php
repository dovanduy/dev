<?php

namespace Api\Controller;

class ContactsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Contacts\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Contacts'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Contacts\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Contacts'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Contacts\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Contacts'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Contacts\All::getInstance()->execute(
            $this->getServiceLocator()->get('Contacts'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Contacts\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Contacts'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Contacts\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Contacts'),
            $this->getParams()
        );
    }    
   
    public function searchAction()
    {
        return \Api\Bus\Contacts\Search::getInstance()->execute(
            $this->getServiceLocator()->get('Contacts'),
            $this->getParams()
        );
    }
    
}
