<?php

namespace Web\Form\User;

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
                'name' => 'email',
                'attributes' => array(
                    'id' => 'email',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Email',
                ),
            ),
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Name',
                ),
            ),
            array(
                'name' => 'mobile',
                'attributes' => array(
                    'id' => 'mobile',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Mobile',
                ),
            ),
            array(
                'name' => 'phone',
                'attributes' => array(
                    'id' => 'phone',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Home phone',
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
                        'email-asc' => 'Email ASC',
                        'email-desc' => 'Email DESC',
                        'name-asc' => 'Name ASC',
                        'name-desc' => 'Name DESC'
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