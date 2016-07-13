<?php

namespace Api\Controller;

class BloggerpostidsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\BloggerPostIds\Add::getInstance()->execute(
            $this->getServiceLocator()->get('BloggerPostIds'),
            $this->getParams()
        );
    }   
    
    public function allAction()
    {
        return \Api\Bus\BloggerPostIds\All::getInstance()->execute(
            $this->getServiceLocator()->get('BloggerPostIds'),
            $this->getParams()
        );
    }
    
}
