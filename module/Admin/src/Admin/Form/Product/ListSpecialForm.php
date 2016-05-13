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
class ListSpecialForm extends AbstractForm
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
        return array(           
            array(
                'name' => 'addProduct',
                'attributes' => array(
                    'type' => 'button',
                    'value' => 'Add Product',
                    'class' => 'btn btn-primary show-model',
                    'data-modelid' => '#add-product-modal',
                ),
            )
        );
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
            array(            
                'name' => 'remove',
                'type' => 'link',
                'title' => 'x',
                'innerHtml' => '<i class="fa fa-fw fa-remove"></i>',                                
                'attributes' => array(
                    'class' => 'ajax-submit',                   
                    'href' => '#',
                    'data-confirmmessage' => $this->translate('Are you sure?'),
                    'data-callback' => " 
                        var tr = btn.closest(\"tr\"); 
                        tr.remove();
                    ",
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/products', 
                        array(
                            'action' => 'removespecial',                             
                        ),
                        array('query' => array(
                            'tab' => $tab,                             
                            'product_id' => '{product_id}',                             
                        )) 
                    )
                ),                             
            ), 
            /*
            array(            
                'name' => 'product_id',
                'type' => 'link',
                'title' => 'ID', 
                'innerHtml' => '{product_id}', 
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute(
                        'admin/products', 
                        array(
                            'action' => 'detail', 
                            'id' => '{_id}'
                        )
                    )
                )
            ),
            * 
            */
            array(            
                'name' => 'url_image',
                'type' => 'image',
                'title' => 'Photo',                                        
                'attributes' => array(
                    'src' => "{url_image}",
                    'width' => 50
                ),                     
            ),
            array(
                'name' => 'name',
                'type' => 'html',
                'title' => 'Name',  
                'innerHtml' => '
                    <a href="/products/detail/{_id}">{name}</a>
                    <br/>SKU: {code}                    
                    <br/>Model: {model}                    
                ',  
                'sort' => true,                
            ),            
            array(
                'name' => 'price',
                'title' => 'Price',
                'sort' => true,
                'attributes' => array(
                    'number' => true
                ),
            ),            
            array(
                'name' => 'category_name',
                'title' => 'Categories',                
            ),            
            array(
                'name' => 'brand_name',
                'title' => 'Brand',                
            ),            
            array(
                'name' => 'sort',
                'type' => 'text',
                'title' => 'Sort',
                'sort' => 'asc',
                'attributes' => array(
                    'name' => 'sort[{product_id}]',
                    'value' => '{sort}',                    
                    'class' => 'number'
                ),
            )    
        );
    }

}