<?php

namespace Web\Form\Auth;

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
        $request = $this->controller->getRequest();
        $headCookie = $request->getHeaders()->get('Cookie');  
        $remember = isset($headCookie->remember) ? unserialize($headCookie->remember) : array();
        return array(           
            array(
                'name' => 'email',    
                'type' => 'Zend\Form\Element\Email',
                'attributes' => array(
                    'id' => 'email',                    
                    'type' => 'text',
                    'required' => true,
                    'autocomplete' => 'off',
                    'class' => 'form-control',
                    'placeholder' => 'your@gmail.com',
                    'value' => !empty($remember['email']) ? $remember['email'] : ''
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
                    'autocomplete' => 'off',
                    'class' => 'form-control',
                    'value' => !empty($remember['password']) ? $remember['password'] : ''
                ),
                'options' => array(
                    'label' => 'Password',
                ),                  
                'validators' => \Web\Module::getValidatorConfig('general.password')
            ), 
            array(            
                'name' => 'remember',
                'attributes' => array(
                    'id' => 'remember',                    
                    'type' => 'checkbox',
                    'checked' => !empty($remember) ? true : false,
                    'value' => 1,
                ),
                'options' => array(
                    'label' => 'Remember me',
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