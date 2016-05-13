<?php

namespace Api\Controller;

class AdminsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Admins\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Admins\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Admins\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Admins\All::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Admins\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Admins\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }
    
    public function updatepasswordAction() 
    {
        return \Api\Bus\Admins\UpdatePassword::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Admins\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }
    
    public function loginAction()
    {
        return \Api\Bus\Admins\Login::getInstance()->execute(
            $this->getServiceLocator()->get('Admins'),
            $this->getParams()
        );
    }
    
}
