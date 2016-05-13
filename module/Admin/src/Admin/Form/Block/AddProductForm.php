<?php

namespace Admin\Form\Block;

use Application\Lib\Api;
use Application\Lib\Arr;
use Application\Form\AbstractForm;

/**
 * List Form
 *
 * @package    Admin\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AddProductForm extends AbstractForm
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
        $tab = $this->getController()->params()->fromQuery('tab');
        $products = Api::call('url_products_all', array('active' => 1)); 
        $elements = array( 
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'product_id',
                'options' => array(                    
                    'label' => 'Product',
                    'value_options' =>
                        array('' => '--Select one--')   
                        + Arr::keyValue($products, 'product_id', 'name')
                ),
                'attributes' => array(
                    'id' => 'product_id',
                    'class' => 'form-control',
                   
                ),
            ) 
        );          
        $elements = array_merge(
            $elements,
            array(
                array(
                    'name' => 'save',
                    'attributes' => array(
                        'type'  => 'submit',
                        'value' => 'Save',
                        'id' => 'saveButton',
                        'class' => 'btn btn-primary ajax-submit',                   
                        'data-callback' => "                            
                            $('#modal-message').html(result.message).show(); 
                            window.location.reload();
                        ",  
                    ),
                ),
                array(
                    'name' => 'cancel',
                    'attributes' => array(
                        'type'  => 'button',
                        'value' => $this->translate('Close'),                  
                        'class' => 'btn',
                        'data-dismiss' => 'modal',
                    ),
                )
            )
        );
        return $elements;
    }
}