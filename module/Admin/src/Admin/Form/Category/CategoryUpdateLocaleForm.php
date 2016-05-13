<?php

namespace Admin\Form\Category;

use Application\Form\AbstractForm;
use Zend\Validator\StringLength;

/**
 * PlaceForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class CategoryUpdateLocaleForm extends AbstractForm
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
                'name' => 'parent_id',
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
                    'label' => 'Name', 
                ),
                'validators' => \Admin\Module::getValidatorConfig('place_locales.name')
            ),
            array(            
                'name' => 'short',
                'attributes' => array(
                    'id' => 'short',                    
                    'type' => 'textarea',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Short',
                ),  
                'validators' => \Admin\Module::getValidatorConfig('place_locales.short')
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
                'validators' => \Admin\Module::getValidatorConfig('place_locales.content')
            ),
            array(
                'name' => 'submit',
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
                    'value' => $this->translate('Cancel'),                  
                    'class' => 'btn',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/places') . "'"
                ),
            )
        );
    }
    
}