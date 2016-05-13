<?php

namespace Api\Controller;

class NewsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\News\Add::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\News\Update::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\News\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\News\All::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\News\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\News\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\News\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\News\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('News'),
            $this->getParams()
        );
    }
}
