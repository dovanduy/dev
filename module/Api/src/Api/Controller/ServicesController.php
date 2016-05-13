<?php

namespace Api\Controller;

class ServicesController extends AppController {

    public function indexAction() {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Services\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Services'),
            $this->getParams()
        );
    }
    
    public function updateAction()
    {
        return \Api\Bus\Services\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Services'),
            $this->getParams()
        );
    }
    
    public function listsAction()
    {
        return \Api\Bus\Services\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Services'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Services\Detail::getInstance()->execute(
                $this->getServiceLocator()->get('Services'),
                $this->getParams()
        );
    }
    
    public function deleteAction()
    {
        return \Api\Bus\Services\Delete::getInstance()->execute(
                $this->getServiceLocator()->get('Services'),
                $this->getParams()
        );
    }
}
