<?php

namespace Api\Controller;

class NewscategoriesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\NewsCategories\Add::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\NewsCategories\Update::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\NewsCategories\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\NewsCategories\All::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\NewsCategories\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\NewsCategories\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\NewsCategories\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\NewsCategories\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('NewsCategories'),
            $this->getParams()
        );
    }
}
