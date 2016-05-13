<?php

namespace Api\Controller;

class BannersController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Banners\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Banners\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Banners\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Banners\All::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Banners\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Banners\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\Banners\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Banners\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Banners'),
            $this->getParams()
        );
    }
}
