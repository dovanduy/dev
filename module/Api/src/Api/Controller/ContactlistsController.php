<?php

namespace Api\Controller;

class ContactlistsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function sendmailAction()
    {
        return \Api\Bus\ContactLists\SendMail::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\ContactLists\All::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
    
}
