<?php

namespace Api\Controller;

class ProductcolorsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\ProductColors\Add::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\ProductColors\Update::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\ProductColors\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\ProductColors\All::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\ProductColors\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\ProductColors\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\ProductColors\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\ProductColors\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('ProductColors'),
            $this->getParams()
        );
    }
}
