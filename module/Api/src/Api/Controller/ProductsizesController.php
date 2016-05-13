<?php

namespace Api\Controller;

class ProductSizesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\ProductSizes\Add::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\ProductSizes\Update::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\ProductSizes\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\ProductSizes\All::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\ProductSizes\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\ProductSizes\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\ProductSizes\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\ProductSizes\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('ProductSizes'),
            $this->getParams()
        );
    }
}
