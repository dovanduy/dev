<?php

namespace Web\Form\User;

use Application\Form\AbstractForm;

/**
 * Change Password Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class NewPasswordForm extends AbstractForm
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
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf',
                'options' => array(
                    'csrf_options' => array(
                        'timeout' => 600
                    )
                )
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
        );
        return $elements;
    }
    
}