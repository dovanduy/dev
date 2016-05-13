<?php

namespace Web\Form\Contact;

use Application\Form\AbstractForm;

/**
 * Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class Form extends AbstractForm
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
        $elements = array(      
            
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Full name',
                ),
                'validators' => \Web\Module::getValidatorConfig('contact.name')
            ), 
            array(            
                'name' => 'email',
                'type' => 'Zend\Form\Element\Email',
                'attributes' => array(
                    'id' => 'email',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Email',
                ),
                'validators' => \Web\Module::getValidatorConfig('general.email')
            ),       

            array(
                'name' => 'phone',
                'attributes' => array(
                    'id' => 'phone',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Phone',
                )
            ), 
            
            array(
                'name' => 'subject',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'subject',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Subject',
                ),
                'validators' => \Web\Module::getValidatorConfig('contact.subject')
            ),
            array(
                'name' => 'content',
                'attributes' => array(
                    'id' => 'content',
                    'type' => 'textarea',
                    'required' => true,
                    'class' => 'form-control',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'Content',                            
                ),
                'validators' => \Web\Module::getValidatorConfig('contact.content')
            ),
        );          
        $elements = array_merge(
            $elements,
            array(
                array(
                    'name' => 'send',
                    'attributes' => array(
                        'type'  => 'submit',
                        'value' => 'Send',
                        'id' => 'submitbutton',
                        'class' => 'submit-button btn btn-default'                    
                    ),
                ),                
            )
        );
        return $elements;
    }
    
}