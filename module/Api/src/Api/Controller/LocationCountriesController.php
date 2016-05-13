<?php

namespace Api\Controller;

class LocationCountriesController extends AppController { 
    
    public function __construct()
    {
        
    }
    
    public function allAction() 
    {        
        return \Api\Bus\LocationCountries\All::getInstance()->execute(
            $this->getServiceLocator()->get('LocationCountries'),
            $this->getParams()
        );
    }
    
}
