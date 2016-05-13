<?php

namespace Api\Controller;

class InputfieldsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\InputFields\Add::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\InputFields\Update::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\InputFields\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\InputFields\All::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\InputFields\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\InputFields\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\InputFields\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\InputFields\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }
    
    public function saveAction()
    {
        return \Api\Bus\InputFields\Save::getInstance()->execute(
            $this->getServiceLocator()->get('InputFields'),
            $this->getParams()
        );
    }
    
    
}
