<?php

namespace Api\Controller;

class CategoriesController extends AppController {
    
    public function __construct()
    {
        
    }
    public function addAction()
    {
        return \Api\Bus\Categories\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Categories'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Categories\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Categories'),
            $this->getParams()
        );
    }
    public function listsAction() 
    {
        return \Api\Bus\Categories\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Categories'),
            $this->getParams()
        );
    }
    public function allAction()
    {
        return \Api\Bus\Categories\All::getInstance()->execute(
            $this->getServiceLocator()->get('Categories'),
            $this->getParams()
        );
    }
    public function detailAction()
    {
        return \Api\Bus\Categories\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Categories'),
            $this->getParams()
        );
    }
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Categories\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Categories'),
            $this->getParams()
        );
    }

}
