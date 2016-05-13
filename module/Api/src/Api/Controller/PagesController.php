<?php

namespace Api\Controller;

class PagesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Pages\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Pages\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Pages\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Pages\All::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Pages\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Pages\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\Pages\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Pages\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Pages'),
            $this->getParams()
        );
    }
}
