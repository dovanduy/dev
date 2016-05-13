<?php

namespace Api\Controller;

class PlacesController extends AppController { 
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function listsAction() 
    {   
        return \Api\Bus\Places\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Places'),
            $this->getParams()
        );
    }

    public function addAction()
    {
        return \Api\Bus\Places\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Places'),
            $this->getParams()
        );
    }

    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Places\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Places'),
            $this->getParams()
        );
    }
    
    public function updateAction()
    {
        return \Api\Bus\Places\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Places'),
            $this->getParams()
        );
    }

    public function detailAction()
    {
        return \Api\Bus\Places\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Places'),
            $this->getParams()
        );
    }
    
    public function onoffAction()
    {
        return \Api\Bus\Places\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Places'),
            $this->getParams()
        );
    }
    
}
