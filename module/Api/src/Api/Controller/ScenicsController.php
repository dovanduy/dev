<?php

namespace Api\Controller;
class ScenicsController extends AppController { 
    
    public function __construct()
    {
    }

    public function listsAction() 
    {
        return \Api\Bus\Scenics\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Scenics'),
            $this->getParams()
        ); 
    } 
    public function detailAction()
    {   
        return \Api\Bus\Scenics\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Scenics'),
            $this->getParams()
        );
    } 
    public function addAction() 
    {   
        return \Api\Bus\Scenics\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Scenics'),
            $this->getParams()
        );
    } 
    public function updateAction() 
    {   
        return \Api\Bus\Scenics\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Scenics'),
            $this->getParams()
        );
    }
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Scenics\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Scenics'),
            $this->getParams()
        );
    }
    
    
    public function onoffAction()
    {
        return \Api\Bus\Scenics\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Scenics'),
            $this->getParams()
        );
    }
    
}
