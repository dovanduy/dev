<?php

namespace Api\Controller;

class FacebookwallsharesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function allAction()
    {
        return \Api\Bus\FacebookWallShares\All::getInstance()->execute(
            $this->getServiceLocator()->get('FacebookWallShares'),
            $this->getParams()
        );
    }
    
    public function addAction()
    {
        return \Api\Bus\FacebookWallShares\Add::getInstance()->execute(
            $this->getServiceLocator()->get('FacebookWallShares'),
            $this->getParams()
        );
    } 
    
}
