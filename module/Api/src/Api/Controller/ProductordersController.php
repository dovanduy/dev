<?php

namespace Api\Controller;

class ProductordersController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        $param = $this->getParams();        
        return \Api\Bus\ProductOrders\Add::getInstance()->execute(
            $this->getServiceLocator(),
            $param
        );
    }

    public function updateAction()
    {
        return \Api\Bus\ProductOrders\Update::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\ProductOrders\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\ProductOrders\All::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\ProductOrders\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\ProductOrders\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\ProductOrders\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\ProductOrders\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
    public function addproductAction()
    {
        return \Api\Bus\ProductOrders\AddProduct::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
    public function onoffproductAction()
    {
        return \Api\Bus\ProductOrders\OnOffProduct::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
    public function savedetailAction()
    {
        return \Api\Bus\ProductOrders\SaveDetail::getInstance()->execute(
            $this->getServiceLocator()->get('ProductOrders'),
            $this->getParams()
        );
    }
    
}
