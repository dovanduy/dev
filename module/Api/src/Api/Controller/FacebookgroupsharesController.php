<?php

namespace Api\Controller;

class FacebookgroupsharesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function allAction()
    {
        return \Api\Bus\FacebookGroupShares\All::getInstance()->execute(
            $this->getServiceLocator()->get('FacebookGroupShares'),
            $this->getParams()
        );
    }
    
    public function addAction()
    {
        return \Api\Bus\FacebookGroupShares\Add::getInstance()->execute(
            $this->getServiceLocator()->get('FacebookGroupShares'),
            $this->getParams()
        );
    } 
    
}
