<?php

namespace Api\Controller;

class ShareurlsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\ShareUrls\Add::getInstance()->execute(
            $this->getServiceLocator()->get('ShareUrls'),
            $this->getParams()
        );
    }
    
    public function updatepostidAction()
    {
        return \Api\Bus\ShareUrls\UpdatePostId::getInstance()->execute(
            $this->getServiceLocator(),
            $this->getParams()
        );
    }
}
