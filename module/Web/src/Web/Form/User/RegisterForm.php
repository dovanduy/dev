<?php

namespace Web\Form\User;

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
class RegisterForm extends AbstractForm
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
                'name' => 'password',
                'attributes' => array(
                    'id' => 'password',                    
                    'type' => 'password',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Password',
                ),
                'validators' => \Web\Module::getValidatorConfig('general.password')
            ),
            array(            
                'name' => 'password_confirmation',
                'attributes' => array(
                    'id' => 'password_confirmation',                    
                    'type' => 'password',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Password confirmation',
                ),
                'validators' => \Web\Module::getValidatorConfig('general.password_confirmation')
            ),
            array(
                'name' => 'mobile',
                'attributes' => array(
                    'id' => 'mobile',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Mobile',
                ),
                'validators' => \Web\Module::getValidatorConfig('general.mobile')
            ),  
        );          
        $elements = array_merge(
            $elements,
            array(
                array(
                    'name' => 'send',
                    'attributes' => array(
                        'type'  => 'submit',
                        'value' => 'Sign Up',
                        'id' => 'submitbutton',
                        'class' => 'submit-button btn btn-default'                    
                    ),
                ),                
            )
        );
        return $elements;
    }
    
}