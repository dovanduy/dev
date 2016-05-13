<?php

namespace Admin\Form\Festival;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Model\LocaleCountries;
use Zend\Validator\StringLength;

/**
 * FestivalAddForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      caolp
 * @copyright   YouGo INC
 */
class FestivalAddForm extends AbstractForm
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
                    'onchange' => 'return localecity(this.value);'
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'city_code',
                'options' => array(
                    'label' => 'State',
                    'value_options' => array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id' => 'city_code',
                    'class' => 'form-control',
                )
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
                'name' => 'lat',              
                'attributes' => array(
                    'id' => 'lat',                    
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Latitude', 
                ),                
            ),
            array(
                'name' => 'lng',              
                'attributes' => array(
                    'id' => 'lng',                    
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Longitude', 
                ),                
            ),
            array(
                'type' => 'Application\Form\Element\DateCalendar',
                'name' => 'start_at',
                'options' => array(                    
                    'label' => 'Start at',
                ),
                'attributes' => array(
                    'id' => 'start_at',
                    'required' => false,
                    'class' => 'form-control',
                    'value' => ''
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.date')
            ),
            array(
                'name' => 'starts_time',
                'attributes' => array(
                    'id' => 'starts_time',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Starts time',
                ),
            ),
            array(
                'type' => 'Application\Form\Element\DateCalendar',
                'name' => 'ends_at',
                'options' => array(
                    'label' => 'End at',
                ),
                'attributes' => array(
                    'id' => 'ends_at',
                    'required' => false,
                    'class' => 'form-control',
                    'value' => ''
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.date')
            ),
            array(
                'name' => 'ends_time',
                'attributes' => array(
                    'id' => 'ends_time',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'End time',
                ),
            ),
            array(
                'name' => 'weekly',
                'attributes' => array(
                    'id' => 'weekly',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Weekly',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.number')
            ),
            array(
                'name' => 'regularly',
                'attributes' => array(
                    'id' => 'regularly',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Regularly',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.number')
            ),
            array(
                'name' => 'name',              
                'attributes' => array(
                    'id' => 'name',                    
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Name', 
                ),
                'validators' => \Admin\Module::getValidatorConfig('festivals.name')
            ),
            array(            
                'name' => 'tag',
                'attributes' => array(
                    'id' => 'tag',                    
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Tag',
                ),     
                'validators' => \Admin\Module::getValidatorConfig('festivals.tag')
            ),
            array(            
                'name' => 'short',
                'attributes' => array(
                    'id' => 'short',                    
                    'type' => 'textarea',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Short',
                ),  
                'validators' => \Admin\Module::getValidatorConfig('festivals.short')
            ),
            array(           
                'name' => 'content',
                'type' => 'Application\Form\Element\CKEditor',
                'attributes' => array(
                    'id' => 'content',
                    'required' => true,
                    'class' => 'form-control',
                    'height' => 300
                ),
                'options' => array(
                    'label' => 'Content',
                ),
                'validators' => \Admin\Module::getValidatorConfig('festivals.content')
            ),
            array(            
                'name' => 'content_mobile',
                'attributes' => array(
                    'id' => 'content_mobile',                    
                    'type' => 'textarea',
                    'class' => 'form-control',
                    'rows' => 8
                ),
                'options' => array(
                    'label' => 'Content for mobile',
                )                
            ),
            array(
                'name' => 'url_image',
                'attributes' => array(
                    'id' => 'url_image',
                    'type' => 'file',
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Main photo',
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
            ),
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'saveBasicbutton',
                    'class' => 'btn btn-primary'                    
                ),
            ),
            array(
                'name' => 'cancel',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => $this->translate('Cancel'),                  
                    'class' => 'btn',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/festivals') . "'"
                ),
            )
        );
    }
    
}