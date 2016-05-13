<?php

namespace Api\Controller;

class BrandsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Brands\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Brands\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Brands\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Brands\All::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Brands\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Brands\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\Brands\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Brands\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Brands'),
            $this->getParams()
        );
    }
}
