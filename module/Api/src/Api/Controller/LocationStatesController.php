<?php

namespace Api\Controller;

class LocationstatesController extends AppController { 
    
    public function __construct()
    {
        
    }
    
    public function allAction() 
    {        
        return \Api\Bus\LocationStates\All::getInstance()->execute(
            $this->getServiceLocator()->get('LocationStates'),
            $this->getParams()
        );
    }
    
}
