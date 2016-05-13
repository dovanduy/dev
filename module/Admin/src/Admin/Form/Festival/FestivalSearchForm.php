<?php

namespace Admin\Form\Festival;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Model\LocaleCities;
use Application\Model\LocaleCountries;

/**
 * FestivalForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      caolp
 * @copyright   YouGo INC
 */
class FestivalSearchForm extends AbstractForm
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
                'name' => 'street',
                'attributes' => array(
                    'id' => 'street',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Street',
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'country_code',
                'options' => array(                    
                    'label' => 'Country',
                    'value_options' =>
                        array('' => '--Select one--') + 
                        LocaleCountries::getAll()                  
                ),
                'attributes' => array(
                    'id' => 'country',
                    'class' => 'form-control',
                    'value' => '',
                    'onchange' => 'return localestate(this.value);'
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'state_code',
                'options' => array(                    
                    'label' => 'State',
                    'value_options' => array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id' => 'state_code',
                    'class' => 'form-control',
                    'value' => '',
                    'onchange' => 'return localecity(this.value);'
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'city_code',
                'options' => array(
                    'label' => 'City',
                    'value_options' =>
                        array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id' => 'city_code',
                    'class' => 'form-control',
                    'value' => '',
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'sort',
                'options' => array(                    
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',
                        'city_code-asc' => 'City code ASC',
                        'city_code-desc' => 'City code DESC',
                        'country_code-asc' => 'Country code ASC',
                        'country_code-desc' => 'Country code DESC',
                        'state_code-asc' => 'State code ASC',
                        'state_code-desc' => 'State code DESC', 
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