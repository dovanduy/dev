<?php

namespace Api\Controller;

class ProductfacebookpagesharesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\ProductFacebookPageShares\Add::getInstance()->execute(
            $this->getServiceLocator()->get('ProductFacebookPageShares'),
            $this->getParams()
        );
    } 
    
}
