<?php

namespace Admin\Form\User;

use Application\Form\AbstractForm;

/**
 * Update Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class UpdateForm extends AbstractForm
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
                'name' => '_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),   
            array(
                'name' => 'image_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'url_image',
                'attributes' => array(
                    'id' => 'url_image',
                    'type' => 'file',
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Main photo',
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
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
                'validators' => \Admin\Module::getValidatorConfig('general.email')
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
                    'label' => 'Full name',
                ),
                'validators' => \Admin\Module::getValidatorConfig('users.name')
            ), 
            array(
                'name' => 'display_name',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'display_name',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Display name',
                ),
                'validators' => \Admin\Module::getValidatorConfig('users.display_name')
            ),
            array(
                'type' => 'Application\Form\Element\DateCalendar',
                'name' => 'birthday',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'Birthday',
                ),
                'attributes' => array(
                    'id' => 'birthday',
                    'required' => false,
                    'class' => 'form-control',
                    'value' => ''
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.date')
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'gender',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'Gender',
                    'value_options' => 
                        array('' => '--Select one--')
                        + \Admin\Module::getConfig('gender_value_options')
                ),
                'attributes' => array(
                    'id' => 'gender',
                    'class' => 'form-control',   
                    'required' => false,
                )
            ),
            array(
                'name' => 'passport',
                'attributes' => array(
                    'id' => 'passport',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Passport',
                )
            ),
            array(
                'name' => 'identify',
                'attributes' => array(
                    'id' => 'identify',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Identify',
                )
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
                    'label' => 'Home phone',
                )
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
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'address_id',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'Main Address',
                    'value_options' => array()
                ),
                'attributes' => array(
                    'id' => 'address_id',
                    'class' => 'form-control',   
                    'required' => false,
                )
            ),
        );  
        if (!empty($this->getController()->params()->fromQuery('backurl'))) {
            $elements[] = array(
                'name' => 'saveAndBack',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Update And Back',
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
                        'value' => 'Update',
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
                        'onclick' => "location.href='" . base64_decode($this->getController()->params()->fromQuery('backurl')) . "'"
                    ),
                )
            )
        );
        return $elements;
    }
    
}