<?php

namespace Api\Controller;

class MenusController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Menus\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Menus\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Menus\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Menus\All::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Menus\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Menus\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\Menus\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Menus\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Menus'),
            $this->getParams()
        );
    }
    
}
