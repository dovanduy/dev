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
    
}
