<?php

namespace Api\Controller;

class AlbumsController extends AppController { 
    
    public function __construct()
    {
        
    }
    
    public function addAction() 
    {
        return \Api\Bus\Albums\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Albums'),
            $this->getParams()
        );
    }
    
    public function updateAction() 
    {
        return \Api\Bus\Albums\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Albums'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Albums\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Albums'),
            $this->getParams()
        );
    }
    
    public function detailAction() 
    {
        return \Api\Bus\Albums\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Albums'),
            $this->getParams()
        );
    }
    
    public function deleteAction() 
    {
        return \Api\Bus\Albums\Delete::getInstance()->execute(
            $this->getServiceLocator()->get('Albums'),
            $this->getParams()
        );
    }
    
    public function updateiseqAction() 
    {
        return \Api\Bus\Albums\UpdateIseq::getInstance()->execute(
            $this->getServiceLocator()->get('Albums'),
            $this->getParams()
        );
    }
    
}
