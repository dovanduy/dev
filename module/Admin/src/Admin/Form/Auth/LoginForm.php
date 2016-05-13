<?php

namespace Admin\Form\Auth;

use Application\Form\AbstractForm;

/**
 * AuthForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class LoginForm extends AbstractForm
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
                'type' => 'Zend\Form\Element\Email',
                'attributes' => array(
                    'id' => 'email',                    
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control',
                    'value' => 'thailvn@gmail.com'
                ),
                'options' => array(
                    'label' => 'Email', 
                ), 
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\EmailAddress',
                        'options' => array(                            
                            
                        ),
                    ),
                ), 
            ),
            array(            
                'name' => 'password',
                'attributes' => array(
                    'id' => 'password',                    
                    'type' => 'password',
                    'required' => true,
                    'class' => 'form-control',
                    'value' => '123456'
                ),
                'options' => array(
                    'label' => 'Password',
                ),  
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(                            
                            'min' => 6
                        ),
                    ),
                ),
            ),            
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Login',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary'                    
                ),
            )            
        );
    }
    
}