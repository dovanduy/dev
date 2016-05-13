<?php

namespace Admin\Form\Scenics;

use Admin\Module as Config;
use Application\Form\AbstractForm;
use Application\Model\LocaleCountries;
use Zend\Validator\StringLength;

/**
 * PlaceAddForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ScenicAddForm extends AbstractForm
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
                    'label'         => 'Country',
                    'value_options' =>
                        array('' => '--Select one--') + 
                        LocaleCountries::getAll()                 
                ),
                'attributes' => array(
                    'id'       => 'country',
                    'class'    => 'form-control',
                    'value'    => '',
                    'onchange' => 'return localestate(this.value);'
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'state_code',
                'options' => array(                    
                    'label'         => 'State',
                    'value_options' => array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id'     => 'state_code',
                    'class'  => 'form-control',                    
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'is_verified',
                'options'           => array(                    
                    'label'         => 'Verified',
                    'value_options' => Config::getConfig('yesno_value_options')                        
                ),
                'attributes' => array(
                    'id'     => 'is_verified',
                    'class'  => 'form-control',
                )
            ),  
            array(
                'name' => 'lat',              
                'attributes' => array(
                    'id'     => 'lat',                    
                    'type'   => 'text',
                    'class'  => 'form-control'
                ),
                'options' => array(
                    'label' => 'Latitude', 
                ),                
            ),
            array(
                'name'       => 'lng',              
                'attributes' => array(
                    'id'     => 'lng',                    
                    'type'   => 'text',
                    'class'  => 'form-control'
                ),
                'options'   => array(
                    'label' => 'Longitude', 
                ),                
            ),
            array(
                'name' => 'url_website', 
                'type' => 'Zend\Form\Element\Url',
                'allow_empty'  => true,
                'attributes'   => array(
                    'id'       => 'url_website',                    
                    'type'     => 'text',
                    'required' => false,
                    'class'    => 'form-control'
                ),
                'options'   => array(
                    'label' => 'Website', 
                ),    
                'validators' => Config::getValidatorConfig('general.uri')
            ),
            array(
                'type' => 'Application\Form\Element\DateCalendar',
                'name' => 'expired_at',
                'options'   => array(                    
                    'label' => 'Expired',
                ),
                'attributes'   => array(
                    'id'       => 'expired_at',
                    'required' => false,
                    'class'    => 'form-control',
                    'value'    => ''
                ),
                'validators' => Config::getValidatorConfig('scenis.expired_at')
            ),
            array(
                'name' => 'url_image',                
                'attributes'     => array(
                    'id'         => 'url_image',    
                    'type'       => 'file',   
                    'required'   => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label'       => 'Image',
                    'is_image'    => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => Config::getValidatorConfig('general.image')
            ),
            array(
                'name' => 'name',              
                'attributes'   => array(
                    'id'       => 'name',                    
                    'type'     => 'text',
                    'required' => true,
                    'class'    => 'form-control'
                ),
                'options'   => array(
                    'label' => 'Name', 
                ),
                'validators' => Config::getValidatorConfig('scenic_locales.name')
            ),
            array(            
                'name' => 'tag',
                'attributes' => array(
                    'id'       => 'tag',                    
                    'type'     => 'text',
                    'required' => true,
                    'class'    => 'form-control'
                ),
                'options' => array(
                    'label' => 'Tag',
                ),     
                'validators' => Config::getValidatorConfig('scenic_locales.tag')
            ),
            array(            
                'name' => 'short',
                'attributes' => array(
                    'id'       => 'short',                    
                    'type'     => 'textarea',
                    'required' => true,
                    'class'    => 'form-control'
                ),
                'options' => array(
                    'label' => 'Short',
                ),  
                'validators' => Config::getValidatorConfig('scenic_locales.short')
            ),
            array(            
                'name' => 'content',
                'attributes'   => array(
                    'id'       => 'content',                    
                    'type'     => 'textarea',
                    'required' => true,
                    'class'    => 'form-control',
                    'rows'     => 4
                ),
                'options'   => array(
                    'label' => 'Content',
                ),
                'validators' => Config::getValidatorConfig('scenic_locales.content')
            ),
            array(            
                'name' => 'content_mobile',
                'attributes' => array(
                    'id'    => 'content_mobile',                    
                    'type'  => 'textarea',
                    'class' => 'form-control',
                    'rows'  => 4
                ),
                'options'   => array(
                    'label' => 'Content for mobile',
                )                
            ),
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'   => 'submit',
                    'value'  => 'Save',
                    'id'     => 'saveBasicbutton',
                    'class'  => 'btn btn-primary'                    
                ),
            ),
            array(
                'name' => 'cancel',
                'attributes'  => array(
                    'type'    => 'button',
                    'value'   => 'Cancel',                  
                    'class'   => 'btn',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/scenics') . "'"
                ),
            )
        );
    }
    
}