<?php

namespace Api\Controller;

class InputoptionsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\InputOptions\Add::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\InputOptions\Update::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\InputOptions\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\InputOptions\All::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\InputOptions\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\InputOptions\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\InputOptions\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\InputOptions\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }
    
    public function saveAction()
    {
        return \Api\Bus\InputOptions\Save::getInstance()->execute(
            $this->getServiceLocator()->get('InputOptions'),
            $this->getParams()
        );
    }
}
