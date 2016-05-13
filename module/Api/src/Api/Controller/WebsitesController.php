<?php

namespace Api\Controller;

class WebsitesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Websites\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Websites\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Websites\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Websites\All::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Websites\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Websites\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\Websites\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Websites\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Websites'),
            $this->getParams()
        );
    }
}
