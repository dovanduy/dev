<?php

namespace Admin\Form\Banner;

use Application\Form\AbstractForm;

/**
 * UpdateLocaleForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class UpdateLocaleForm extends AbstractForm
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
                'name' => '_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'locale',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),            
            array(            
                'name' => 'title',
                'attributes' => array(
                    'id' => 'title',
                    'type' => 'textarea',
                    'required' => false,
                    'class' => 'form-control',
                    'rows' => 2
                ),
                'options' => array(
                    'label' => 'Title',
                ),               
            ),
            array(
                'name' => 'short',
                'attributes' => array(
                    'id' => 'short',
                    'type' => 'textarea',
                    'required' => true,
                    'class' => 'form-control',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'Short',                    
                ),               
            ),            
            array(
                'name' => 'saveAndBack',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Update And Back',
                    'id' => 'saveAndBackButton',
                    'class' => 'btn btn-primary'                    
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