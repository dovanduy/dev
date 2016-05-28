<?php

namespace Admin\Form\Product;

use Application\Form\AbstractForm;
use Application\Model\ProductColors;

/**
 * Category Image Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ImageForm extends AbstractForm
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
        
        $productId = $this->getAttribute('product_id');
        $main = $this->getAttribute('main');
        $colors = ProductColors::getAll(false, $productId);
        $elements = array();
        for ($i = 1; $i < \Application\Module::getConfig('products.max_images'); $i++) {
            $name = 'url_image' . $i;
            $elements[] = array(
                'name' => $name, 
                'type' => 'Application\Form\Element\Image',
                'attributes' => array(
                    'id' => $name, 
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Image ' . $i,                   
                    'allow_empty' => true, // custom
                    'colors' => $colors, // custom
                    'is_main' => ($name == $main ? 1 : 0), // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
            );
        }        
        $elements = array_merge(
            $elements,
            array(
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
                        'value' => 'Update',
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
            )
        );
        return $elements;
    }
    
}