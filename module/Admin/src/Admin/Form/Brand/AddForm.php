<?php

namespace Admin\Form\Brand;

use Application\Form\AbstractForm;

/**
 * Category Add Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AddForm extends AbstractForm
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
                'name' => 'url_image',
                'attributes' => array(
                    'id' => 'url_image',
                    'type' => 'file',
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Logo',
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
            ),
            array(            
                'name' => 'url',
                'type' => 'Zend\Form\Element\Url',
                'attributes' => array(
                    'id' => 'url',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Url',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
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
                'validators' => \Admin\Module::getValidatorConfig('brands.name')
            ),
            array(
                'name' => 'short',
                'attributes' => array(
                    'id' => 'short',
                    'type' => 'textarea',
                    'required' => false,
                    'class' => 'form-control',
                    'rows' => 8
                ),
                'options' => array(
                    'label' => 'Short',
                ),                
            ),
            array(
                'name' => 'about',
                'type' => 'Application\Form\Element\CKEditor',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'about',
                    'type' => 'textarea',
                    'required' => false,
                    'class' => 'form-control',
                    'height' => 300
                ),
                'options' => array(
                    'label' => 'About',
                ),
                'validators' => \Admin\Module::getValidatorConfig('brands.about')
            ),
            array(
                'name' => 'meta_keyword',
                'attributes' => array(
                    'id' => 'meta_keyword',
                    'type' => 'textarea',                    
                    'class' => 'form-control',
                    'maxlength' => '500',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'SEO keyword',
                )
            ),
            array(
                'name' => 'meta_description',
                'attributes' => array(
                    'id' => 'meta_description',
                    'type' => 'textarea',                    
                    'class' => 'form-control',
                    'maxlength' => '500',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'SEO description',
                )
            ),
            array(
                'name' => 'save',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'saveButton',
                    'class' => 'btn btn-primary'                    
                ),
            ),
            array(
                'name' => 'cancel',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => $this->translate('Cancel'),                  
                    'class' => 'btn',
                    'onclick' => "location.href='" . base64_decode($this->getController()->params()->fromQuery('backurl')) . "'"
                ),
            )
        );
    }
    
}