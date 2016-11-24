<?php

namespace Web\Form\Product;

use Application\Form\AbstractForm;

/**
 * List Form
 *
 * @package Web\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class CopyForm extends AbstractForm
{
    /**
     * Table construct
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Element array to create form
    *
    * @return array Array to create elements for form
    */
    public function elements() {
        $elements = array(           
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
            ),            
        );          
        $elements = array_merge(
            $elements,
            array(                
                array(
                    'name' => 'cancel',
                    'attributes' => array(
                        'type'  => 'button',
                        'value' => 'Close',                  
                        'class' => 'btn',
                        'data-dismiss' => 'modal',
                    ),
                )
            )
        );
        return $elements;
    }
}