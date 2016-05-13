<?php

namespace Admin\Form\Place;

use Application\Form\AbstractForm;
use Zend\Validator\StringLength;

/**
 * PlaceForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class PlaceUpdateLocaleForm extends AbstractForm
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
                'name' => '_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'locale',
                'attributes' => array(
                    'type' => 'hidden',
                ),
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
                'validators' => \Admin\Module::getValidatorConfig('place_locales.name')
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
                'validators' => \Admin\Module::getValidatorConfig('place_locales.tag')
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
                'validators' => \Admin\Module::getValidatorConfig('place_locales.short')
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
                'validators' => \Admin\Module::getValidatorConfig('place_locales.content')
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
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary'                    
                ),
            ),
            array(
                'name' => 'cancel',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => $this->translate('Cancel'),                  
                    'class' => 'btn',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/places') . "'"
                ),
            )
        );
    }
    
}