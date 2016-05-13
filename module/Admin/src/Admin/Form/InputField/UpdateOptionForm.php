<?php

namespace Admin\Form\InputField;

use Application\Form\AbstractForm;

/**
 * Add/Update Option Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class UpdateOptionForm extends AbstractForm
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
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Value',
                ),
                'validators' => \Admin\Module::getValidatorConfig('input_fields.name')
            ),            
            array(
                'name' => 'addOption',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Add',
                    'class' => 'btn btn-primary'                    
                ),
            )
        );
    }
    
}