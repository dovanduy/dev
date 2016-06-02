<?php

namespace Admin\Form\Product;

use Application\Form\AbstractForm;
use Application\Model\ProductSizes;
use Application\Model\ProductColors;

/**
 * Category Add Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AddPriceForm extends AbstractForm
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
        $sizes = ProductSizes::getAll();
        $colors = ProductColors::getAll(); 
        return array(
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'color_id',
                'options' => array(
                    'label' => 'Product Color List',
                    'value_options' => $colors
                ),
                'attributes' => array(
                    'id' => 'color_id',
                    'class' => 'form-control',
                    'multiple' => true
                )
            ),
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'size_id',
                'options' => array(
                    'label' => 'Product Size List',
                    'value_options' => $sizes
                ),
                'attributes' => array(
                    'id' => 'size_id',
                    'class' => 'form-control',
                    'multiple' => true
                )
            ),                     
            array(
                'name' => 'save',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Update',
                    'id' => 'saveButton',
                    'class' => 'btn btn-primary'                    
                ),
            )
        );
    }
    
}