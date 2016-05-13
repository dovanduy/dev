<?php

namespace Api\Controller;

class ProductcategoriesController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\ProductCategories\Add::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\ProductCategories\Update::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\ProductCategories\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\ProductCategories\All::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\ProductCategories\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\ProductCategories\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\ProductCategories\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\ProductCategories\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategories'),
            $this->getParams()
        );
    }
    
    public function addfieldAction()
    {
        return \Api\Bus\ProductCategories\AddField::getInstance()->execute(
            $this->getServiceLocator()->get('ProductCategoryHasFields'),
            $this->getParams()
        );
    }
    
    public function allbrandAction()
    {
        return \Api\Bus\ProductCategories\AllBrand::getInstance()->execute(
            $this->getServiceLocator()->get('ProductHasCategories'),
            $this->getParams()
        );
    }
    
    public function filterAction()
    {
        return \Api\Bus\ProductCategories\Filter::getInstance()->execute(
            $this->getServiceLocator()->get('ProductHasCategories'),
            $this->getParams()
        );
    }
    
}
