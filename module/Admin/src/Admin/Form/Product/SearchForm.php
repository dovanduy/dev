<?php

namespace Admin\Form\Product;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Model\Brands;
use Application\Model\ProductCategories;

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
        $branbs = Brands::getAll();
        $categories = ProductCategories::getForSelect($lastLevel);
        return array(              
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'category_id',
                'options' => array(
                    'label' => 'Product category',
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
                    'label' => 'Brand name',
                    'value_options' => array('0' => '--All--') + $branbs
                ),
                'attributes' => array(
                    'id' => 'brand_id',
                    'class' => 'form-control',
                )
            ),
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Product name',
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'active',
                'options' => array(
                    'label' => 'Active',
                    'value_options' => Module::getConfig('search_active_value_options')
                ),
                'attributes' => array(
                    'id' => 'active',
                    'class' => 'form-control',
                    'value' => ''
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'available',
                'options' => array(
                    'label' => 'Available',
                    'value_options' => Module::getConfig('search_active_value_options')
                ),
                'attributes' => array(
                    'id' => 'available',
                    'class' => 'form-control',
                    'value' => ''
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'available_sendo',
                'options' => array(
                    'label' => 'S-Available',
                    'value_options' => Module::getConfig('search_active_value_options')
                ),
                'attributes' => array(
                    'id' => 'available_sendo',
                    'class' => 'form-control',
                    'value' => ''
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',
                        'product_id-asc' => 'ID ASC',
                        'product_id-desc' => 'ID DESC',
                        'code-asc' => 'SKU ASC',
                        'code-desc' => 'SKU DESC',
                        'name-asc' => 'Name ASC',
                        'name-desc' => 'Name DESC',
                        'sort-asc' => 'Sort ASC',
                        'sort-desc' => 'Sort DESC',                        
                        'updated-desc' => 'Update Date DESC',
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
                    'label' => 'Record/Page',
                    'value_options' => Module::getConfig('search_limit_value_options')
                ),
                'attributes' => array(
                    'id' => 'limit',
                    'class' => 'form-control',
                    'value' => '10'
                )
            ),            
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Search',
                    'id' => 'searchButton',
                    'class' => 'btn btn-primary'
                ),
            )
        );
    }
}