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
class PaymentForm extends AbstractForm
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
				'type' => 'Zend\Form\Element\MultiCheckbox',
                'name' => 'payment',
                'attributes' => array(
                    'id' => 'payment',
                    'type' => 'radio',
                    'required' => false,                    
                ),
                'options' => array(
                    'label' => 'Payment', 
					'value_options' => array(
						'COD' => 'COD',
						'ATM' => 'ATM',
					)    					
                )
            ), 
        );
        return $elements;
    }
    
}