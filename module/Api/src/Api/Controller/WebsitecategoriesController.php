<?php

namespace Api\Controller;

class WebsitecategoriesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\WebsiteCategories\Add::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\WebsiteCategories\Update::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\WebsiteCategories\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\WebsiteCategories\All::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\WebsiteCategories\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\WebsiteCategories\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\WebsiteCategories\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\WebsiteCategories\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('WebsiteCategories'),
            $this->getParams()
        );
    }
}
