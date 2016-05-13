<?php

namespace Api\Controller;

class LocationCitiesController extends AppController { 
    
    public function __construct()
    {
        
    }
    
    public function allAction() 
    {        
        return \Api\Bus\LocationCities\All::getInstance()->execute(
            $this->getServiceLocator()->get('LocationCities'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {        
        return \Api\Bus\LocationCities\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('LocationCities'),
            $this->getParams()
        );
    }
    
}
