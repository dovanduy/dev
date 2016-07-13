<?php

namespace Api\Controller;

class UsersController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Users\Add::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
    
    public function registerAction()
    {
        return \Api\Bus\Users\Register::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Users\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Users\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Users\All::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Users\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Users\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function updatepasswordAction() 
    {
        return \Api\Bus\Users\UpdatePassword::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }

    public function updatenewpasswordAction() 
    {
        return \Api\Bus\Users\UpdateNewPassword::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function onoffAction()
    {
        return \Api\Bus\Users\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function loginAction()
    {
        return \Api\Bus\Users\Login::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function getloginAction()
    {
        return \Api\Bus\Users\GetLogin::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function checkloginAction()
    {
        return \Api\Bus\Users\CheckLogin::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function searchAction()
    {
        return \Api\Bus\Users\Search::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function fbloginAction()
    {
        return \Api\Bus\Users\FbLogin::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
    
    public function gloginAction()
    {
        return \Api\Bus\Users\GLogin::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
    
    public function forgotpasswordAction()
    {
        return \Api\Bus\Users\ForgotPassword::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
    
    public function fbadminAction()
    {
        return \Api\Bus\Users\FbAdmin::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
    public function googleAction()
    {
        return \Api\Bus\Users\Google::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }
    
}
