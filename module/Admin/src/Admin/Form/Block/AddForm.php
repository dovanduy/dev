<?php

namespace Admin\Form\Block;

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
                'validators' => \Admin\Module::getValidatorConfig('blocks.name')
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
                'name' => 'name_url',
                'attributes' => array(
                    'id' => 'name_url',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Url name',
                ),
                'validators' => \Admin\Module::getValidatorConfig('blocks.name')
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