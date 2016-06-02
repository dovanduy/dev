<?php

namespace Admin\Form\Product;

use Application\Form\AbstractForm;

/**
 * Category List Form
 *
 * @package    Admin\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ListPriceForm extends AbstractForm
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
    public function elements()
    {
       
    }

    /**
     * Column array to create table
     *
     * @return array Array to create columns for table
     */
    public function columns()
    {
        $tab = $this->getController()->params()->fromQuery('tab');
        return array( 
               
            /*
            array(            
                'name' => 'url_image',
                'type' => 'image',
                'title' => 'Photo',                                        
                'attributes' => array(
                    'src' => "{url_image}",
                    'width' => 50
                ),                     
            ),
             * 
             */
            array(
                'name' => 'color_name',                
                'title' => 'Color'         
            ),   
            array(
                'name' => 'size_name',                
                'title' => 'Size'         
            ),
            array(
                'name' => 'price',
                'type' => 'text',
                'title' => 'Price',
                'attributes' => array(
                    'name' => 'price[{id}]',
                    'value' => '{price}',
                    'class' => 'number price ajax-change',
                    'data-id' => '{id}',
                    'data-product_id' => '{product_id}',
                    'data-a-sep' => ',',
                    'data-a-dec' => '.',
                    'data-v-max' => '99999999999',
                    'data-v-min' => '0',
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/products', 
                        array(
                            'action' => 'saveprice'
                        ),
                        array(
                            'query' => array(
                                'product_id' => '{product_id}'
                            )
                        )
                    ),                    
                    'data-callback' => " 
                       
                    ",
                ),
            ),
            array(
                'name' => 'active',
                'type' => 'toggle',
                'title' => 'Active',   
                'attributes' => array(
                    'id' => "active",
                    'value' => "{id}"
                ),                              
            ),
        );
    }

}