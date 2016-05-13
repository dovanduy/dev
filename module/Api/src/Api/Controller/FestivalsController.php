<?php

namespace Api\Controller;

class FestivalsController extends AppController {

    public function indexAction() {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Festivals\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Festivals'),
            $this->getParams()
        );
    }
    
    public function updateAction()
    {
        return \Api\Bus\Festivals\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Festivals'),
            $this->getParams()
        );
    }
    
    public function listsAction()
    {
        return \Api\Bus\Festivals\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Festivals'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Festivals\Detail::getInstance()->execute(
                $this->getServiceLocator()->get('Festivals'),
                $this->getParams()
        );
    }

    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Festivals\Delete::getInstance()->execute(
            $this->getServiceLocator()->get('Festivals'),
            $this->getParams()
        );
    }

    public function deleteAction()
    {
        return \Api\Bus\Festivals\Delete::getInstance()->execute(
                $this->getServiceLocator()->get('Festivals'),
                $this->getParams()
        );
    }
}
