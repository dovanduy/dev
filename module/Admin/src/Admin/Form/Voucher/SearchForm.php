<?php

namespace Admin\Form\Voucher;

use Admin\Module;
use Application\Form\AbstractForm;

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
        return array(                                     
            array(
                'name' => 'phone',
                'attributes' => array(
                    'id' => 'phone',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Phone',
                ),
            ), 
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'used',
                'options' => array(
                    'label' => 'Used',
                    'value_options' => Module::getConfig('search_active_value_options')
                ),
                'attributes' => array(
                    'id' => 'used',
                    'class' => 'form-control',
                    'value' => ''
                )
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
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',   
                        'phone-asc' => 'Phone ASC',
                        'phone-desc' => 'Phone DESC',                        
                        'created-desc' => 'Created Date DESC',
                        'updated-desc' => 'Updated Date DESC',
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