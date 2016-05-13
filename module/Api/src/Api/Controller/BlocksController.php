<?php

namespace Api\Controller;

class BlocksController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Blocks\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Blocks\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Blocks\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Blocks\All::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Blocks\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Blocks\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\Blocks\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Blocks\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Blocks'),
            $this->getParams()
        );
    }
    
    public function addproductAction()
    {
        return \Api\Bus\Blocks\AddProduct::getInstance()->execute(
            $this->getServiceLocator()->get('BlockProducts'),
            $this->getParams()
        );
    }
    
    public function removeproductAction()
    {
        return \Api\Bus\Blocks\RemoveProduct::getInstance()->execute(
            $this->getServiceLocator()->get('BlockProducts'),
            $this->getParams()
        );
    }
    
    public function updatesortproductAction()
    {
        return \Api\Bus\Blocks\UpdateSortProduct::getInstance()->execute(
            $this->getServiceLocator()->get('BlockProducts'),
            $this->getParams()
        );
    }
    
}
