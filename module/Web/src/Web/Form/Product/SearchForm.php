<?php

namespace Web\Form\Product;

use Application\Form\AbstractForm;
use Application\Model\Brands;

use Web\Module;
use Web\Model\ProductCategories;

/**
 * SearchForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class SearchForm extends AbstractForm
{
    /**
    * Form construct
    *
    * @param string $name Form name
    */
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    /**
    * Element array to create form
    *
    * @return array Array to create elements for form
    */
    public function elements() {
        $brands = Brands::getAll();
        $categories = ProductCategories::getForSelect($lastLevel);
        return array(              
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'category_id',
                'options' => array(
                    'label' => 'Category',
                    'value_options' =>
                        array('0' => '--All--') +
                        $categories
                ),
                'attributes' => array(
                    'id' => 'category_id',
                    'class' => 'form-control',
                    'value' => '0'
                ),    
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'brand_id',
                'options' => array(
                    'label' => 'Brand',
                    'value_options' => array('0' => '--All--') + $brands
                ),
                'attributes' => array(
                    'id' => 'brand_id',
                    'class' => 'form-control',
                )
            ),                   
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',                        
                        'price-asc' => 'Price ASC',
                        'price-desc' => 'Price DESC',
                        'name-asc' => 'Name ASC',
                        'name-desc' => 'Name DESC',
                    ),
                ),
                'attributes' => array(
                    'id' => 'sort',
                    'class' => 'form-control',
                    'value' => ''
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'limit',
                'options' => array(
                    'label' => 'Limit',
                    'value_options' => Module::getConfig('search_limit_value_options')
                ),
                'attributes' => array(
                    'id' => 'limit',
                    'class' => 'form-control',
                    'value' => '16'
                )
            ),            
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Search',
                    'id' => 'searchButton',
                    'class' => 'btn btn-default'
                ),
            )
        );
    }
}