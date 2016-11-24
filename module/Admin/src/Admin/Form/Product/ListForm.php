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
class ListForm extends AbstractForm
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
                'name' => 'addnew',
                'attributes' => array(
                    'type' => 'button',
                    'value' => $this->translate('Add Product'),
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/products', 
                            array('action' => 'add'),
                            array(
                                'query' => array(
                                    'backurl' => base64_encode($this->getRequest()->getRequestUri())
                                )
                            )
                        ) . "'"
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
        return array( 
            /*
            array(            
                'name' => 'buy',
                'type' => 'link',
                'title' => '+',
                'innerHtml' => '<i class="fa fa-fw fa-cart-arrow-down"></i>', 
                'attributes' => array(
                    'class' => 'ajax-submit',
                    'href' => '#',
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/carts', 
                        array(
                            'action' => 'additem', 
                            'id' => '{_id}'
                        )
                    ),
                    'data-callback' => " 
                        loadCart(1);                        
                        showMessage(\"" . $this->translate('Added to cart') . "\");
                    "                   
                ),                             
            ), 
             * 
             */          
            array(            
                'name' => 'product_id',                
                'title' => 'ID', 
                'innerHtml' => '{product_id}',
                'sort' => true,
                'attributes' => array(
                    'width' => 50
                )
            ),           
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
                'type' => 'link',
                'title' => 'Product name',  
                'innerHtml' => '
                    {name}<br/>
                    <a target="_blank" href="{url_other}">[SENDO] Xem</a><br/>
                    <a target="_blank" href="{sendo_edit_url}">[SENDO] Sửa</a><br/>
                    <a target="_blank" href="{url_src}">Link nguồn</a>
                ',
                'sort' => true,              
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute(
                        'admin/products', 
                        array(
                            'action' => 'detail', 
                            'id' => '{_id}'
                        ),
                        array(
                            'query' => array(
                                'backurl' => base64_encode($this->getRequest()->getRequestUri())
                            )
                        )
                    )
                ), 
            ), 
//            array(
//                'name' => 'name',
//                'type' => 'html',
//                'title' => 'Product name',  
//                'innerHtml' => '<a href="/products/detail/{_id}">{name}</a><br/>' . $this->translate('SKU') . ': {code}',           
//                'sort' => true,                
//            ),            
            array(
                'name' => 'price',
                'title' => 'Price',
                'sort' => true,
                'attributes' => array(
                    'number' => true,
                ),
            ), 
            /*
            array(
                'name' => 'category_name',
                'title' => 'Product Category List',                
            ),            
            array(
                'name' => 'brand_name',
                'title' => 'Brand name',                
            ),            
             * 
             */
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
            ),
            array(
                'name' => 'available',
                'type' => 'toggle',
                'title' => 'Available',   
                'attributes' => array(
                    'id' => "available",
                    'value' => "{_id}",
                    'data-field' => 'available'
                ),                              
            ),
            array(
                'name' => 'available_sendo',
                'type' => 'toggle',
                'title' => 'S-Available ',   
                'attributes' => array(
                    'id' => "available_sendo",
                    'value' => "{_id}",
                    'data-field' => 'available_sendo'
                ),                              
            ),
            array(
                'name' => 'active',
                'type' => 'toggle',
                'title' => 'Active',   
                'attributes' => array(
                    'id' => "active",
                    'value' => "{_id}"
                ),                              
            ),            
            array(            
                'name' => 'edit',
                'type' => 'link',
                'title' => 'Edit',
                'innerHtml' => '<i class="fa fa-fw fa-edit"></i>',                                
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute(
                        'admin/products', 
                        array(
                            'action' => 'update', 
                            'id' => '{_id}'
                        ),
                        array(
                            'query' => array(
                                'backurl' => base64_encode($this->getRequest()->getRequestUri())
                            )
                        )
                    )
                ),                             
            ),            
        );
    }

}