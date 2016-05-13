<?php

namespace Admin\Form\Place;

use Application\Form\AbstractForm;

/**
 * Place Image Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class PlaceImageForm extends AbstractForm
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
        $elements = array();
        for ($i = 1; $i < \Application\Module::getConfig('places.max_images'); $i++) {
            $name = 'url_image' . $i;
            $elements[] = array(
                'name' => $name,                
                'attributes' => array(
                    'id' => $name,    
                    'type' => 'file',   
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Image ' . $i,
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
            );
        }        
        $elements[] = array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'saveButton',
                'class' => 'btn btn-primary first'                    
            ),
        );
        $elements[] = array(
            'name' => 'cancel',
            'attributes' => array(
                'type'  => 'button',
                'value' => $this->translate('Cancel'),                  
                'class' => 'btn',
                'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/places') . "'"
            ),
        );
        return $elements;
    }
    
}