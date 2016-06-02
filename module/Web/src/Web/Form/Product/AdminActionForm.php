<?php

namespace Web\Form\Product;

use Application\Form\AbstractForm;
use Web\Model\ProductColors;

/**
 * Actions for Admin
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AdminActionForm extends AbstractForm
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
        $colors = ProductColors::getAll($productId);
        return array(           
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'color_id',
                'options' => array(
                    'label' => 'Color',
                    'value_options' => $colors
                ),
                'attributes' => array(
                    'id' => 'color_id',
                    'class' => 'form-control',
                    'multiple' => true,
                    'required' => true
                )
            ),
            /*
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'brand_id',
                'options' => array(
                    'label' => 'Brand',
                    'value_options' => array('0' => '--Choose one--') + $branbs
                ),
                'attributes' => array(
                    'id' => 'brand_id',
                    'class' => 'form-control',
                )
            ),
            * 
            */            
            array(
                'name' => 'save',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'saveButton',
                    'class' => 'btn btn-primary'                    
                ),
            )
        );
    }
    
}