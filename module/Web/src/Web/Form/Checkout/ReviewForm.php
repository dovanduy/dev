<?php

namespace Web\Form\Checkout;

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
class ReviewForm extends AbstractForm
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
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf',
                'options' => array(
                    'csrf_options' => array(
                        'timeout' => 600
                    )
                )
            ),
            array(
                'name' => 'note',
                'attributes' => array(
                    'id' => 'note',
                    'type' => 'textarea',
                    'required' => false,
                    'class' => 'form-control',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'Memo',                            
                )
            ), 
        );
        return $elements;
    }
    
}