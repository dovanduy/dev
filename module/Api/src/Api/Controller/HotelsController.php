<?php

namespace Api\Controller;

class HotelsController extends AppController {

    public function indexAction() {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Hotels\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Hotels'),
            $this->getParams()
        );
    }
    
    public function updateAction()
    {
        return \Api\Bus\Hotels\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Hotels'),
            $this->getParams()
        );
    }
    
    public function listsAction()
    {
        return \Api\Bus\Hotels\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Hotels'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Hotels\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Hotels'),
            $this->getParams()
        );
    }
}
