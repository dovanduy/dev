<?php

namespace Admin\Form\ProductOrder;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Model\LocaleCountries;

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
                'name' => 'code',
                'attributes' => array(
                    'id' => 'code',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Code',
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'country_code',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'Country',
                    'value_options' =>
                        array('' => '--All--') + 
                        LocaleCountries::getAll()                 
                ),
                'attributes' => array(
                    'id' => 'country_code',
                    'class' => 'form-control',
                    'onchange' => 'return localeState(this.value);',
                    'required' => false,
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'state_code',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'State/Province',
                    'value_options' => array('' => '--All--')
                ),
                'attributes' => array(
                    'id' => 'state_code',
                    'class' => 'form-control', 
                    'onchange' => 'return localeCity(this.value);',
                    'required' => false,
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'city_code',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'City/Distrcit',
                    'value_options' => array('' => '--All--')
                ),
                'attributes' => array(
                    'id' => 'city_code',
                    'class' => 'form-control', 
                    'required' => false,
                ),
            ),
            array(
                'name' => 'street',
                'attributes' => array(
                    'id' => 'street',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Address (number, street, ward)',
                )
            ),
            array(
                'name' => 'user_name',
                'attributes' => array(
                    'id' => 'user_name',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Customer name',
                ),
            ),
            array(
                'name' => 'user_email',
                'attributes' => array(
                    'id' => 'user_email',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Email',
                ),
            ),
            array(
                'name' => 'user_mobile',
                'attributes' => array(
                    'id' => 'user_mobile',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Mobile',
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
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',                        
                        'code-asc' => 'Code ASC',
                        'code-desc' => 'Code DESC',
                        'user_state_code-asc' => 'State/Province ASC',
                        'user_state_code-desc' => 'State/Province DESC',
                        'user_city_code-asc' => 'City/District ASC',
                        'user_city_code-desc' => 'City/District DESC',
                        'user_street-asc' => 'Address ASC',
                        'user_street-desc' => 'Address DESC',
                        'user_name-asc' => 'Customer name ASC',
                        'user_name-desc' => 'Customer name DESC',
                        'user_email-asc' => 'Customer email ASC',
                        'user_email-desc' => 'Customer email DESC',
                        'user_mobile-asc' => 'Customer mobile ASC',
                        'user_mobile-desc' => 'Customer mobile DESC',
                        'created-asc' => 'Created ASC',
                        'created-desc' => 'Created DESC',
                        'updated-asc' => 'Updated ASC',
                        'updated-desc' => 'Updated DESC',
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