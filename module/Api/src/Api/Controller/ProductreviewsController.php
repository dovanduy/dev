<?php

namespace Api\Controller;

class ProductreviewsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\ProductReviews\Add::getInstance()->execute(
            $this->getServiceLocator()->get('ProductReviews'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\ProductReviews\Update::getInstance()->execute(
            $this->getServiceLocator()->get('ProductReviews'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\ProductReviews\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('ProductReviews'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\ProductReviews\All::getInstance()->execute(
            $this->getServiceLocator()->get('ProductReviews'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\ProductReviews\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('ProductReviews'),
            $this->getParams()
        );
    }
    

    public function onoffAction()
    {
        return \Api\Bus\ProductReviews\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('ProductReviews'),
            $this->getParams()
        );
    }
    
}
