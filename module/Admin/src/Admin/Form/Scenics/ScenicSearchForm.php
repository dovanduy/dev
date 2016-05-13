<?php

namespace Admin\Form\Scenics;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Model\LocaleCountries;

/**
 * PlaceForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ScenicSearchForm extends AbstractForm
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
                    'value' => ''
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'is_verified',
                'options' => array(                    
                    'label' => 'Verified',
                    'value_options' => Module::getConfig('search_yesno_value_options')                        
                ),
                'attributes' => array(
                    'id' => 'is_verified',
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
                        'country_code-asc' => 'Country code ASC',
                        'country_code-desc' => 'Country code DESC',
                        'state_code-asc' => 'State code ASC',
                        'state_code-desc' => 'State code DESC', 
                        'count_read-asc' => 'Total read ASC',
                        'count_read-desc' => 'Total read DESC', 
                        'count_like-asc' => 'Total like ASC',
                        'count_like-desc' => 'Total like DESC', 
                        'count_favourite-asc' => 'Total favourite ASC',
                        'count_favourite-desc' => 'Total favourite DESC', 
                        'count_comment-asc' => 'Total comment ASC',
                        'count_comment-desc' => 'Total comment DESC', 
                        'count_rate-asc' => 'Total rate ASC',
                        'count_rate-desc' => 'Total rate DESC', 
                        'count_rate_person-asc' => 'Total rate/person ASC',
                        'count_rate_person-desc' => 'Total rate/person DESC', 
                        'count_follow-asc' => 'Total follow ASC',
                        'count_follow-desc' => 'Total follow DESC', 
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