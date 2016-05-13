<?php

namespace Api\Controller;

class ImagesController extends AppController { 
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function allAction() 
    {   
        return \Api\Bus\Images\All::getInstance()->execute(
            $this->getServiceLocator()->get('Images'),
            $this->getParams()
        );
    }

    public function addAction()
    {
        return \Api\Bus\Images\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Images'),
            $this->getParams()
        );
    }

    public function detailAction()
    {
        return \Api\Bus\Images\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Images'),
            $this->getParams()
        );
    }
    
}
