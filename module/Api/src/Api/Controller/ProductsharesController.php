<?php

namespace Api\Controller;

class ProductsharesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\ProductShares\Add::getInstance()->execute(
            $this->getServiceLocator()->get('ProductShares'),
            $this->getParams()
        );
    }   
    
    public function allAction()
    {
        return \Api\Bus\ProductShares\All::getInstance()->execute(
            $this->getServiceLocator()->get('ProductShares'),
            $this->getParams()
        );
    }
    
}
