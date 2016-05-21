<?php

namespace Web\Form\User;

use Application\Form\AbstractForm;




/**
 * Form
 *
 * @package 	Web\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ForgetPasswordForm extends AbstractForm
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