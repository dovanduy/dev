<?php

namespace Web\Form\My;

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
class Password2Form extends AbstractForm
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
                'name' => 'password',
                'attributes' => array(
                    'id' => 'password',                    
                    'type' => 'password',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'New password',
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
        if (!empty($this->getController()->params()->fromQuery('backurl'))) {
            $elements[] = array(
                'name' => 'saveAndBack',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save And Back',
                    'id' => 'saveAndBackButton',
                    'class' => 'btn btn-primary'                    
                ),
            );
        }
        $elements = array_merge(
            $elements,
            array(
                array(
                    'name' => 'save',
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
                        'value' => 'Cancel',                  
                        'class' => 'btn',
                        'onclick' => "location.href='" . base64_decode($this->getController()->params()->fromQuery('backurl')) . "'"
                    ),
                )
            )
        );
        return $elements;
    }
    
}