<?php

namespace Web\Form\ProductOrder;

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
class CartProductListForm extends AbstractForm
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
     * Html will embedded after table
     *
     * @return array Array to create columns for table
     */
    public function tfoot()
    {
        $cartItems = $this->getDataset();
        $totalQuantity = 0;
        $totalMoney = 0;
        foreach ($cartItems as $item) {
            $totalQuantity += db_int($item['quantity']);
            $totalMoney += db_int($item['quantity']) * db_float($item['price']);
        }
        return '
            <tr>
                <td colspan="7" align="right">
                    <div class="total-money">' . $this->translate('Total') . ':' . 
                    '<span>' . app_money_format($totalMoney) . '</span></div>
                </td>
            </tr>
        ';
    }
    
    /**
     * Column array to create table
     *
     * @return array Array to create columns for table
     */
    public function columns()
    {
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
                        $(\".total-quantity span\").html(result.totalQuantity);
                        $(\".total-money span\").html(result.totalMoney);           
                        loadCart();
                    ",
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/carts', 
                        array(
                            'action' => 'removeitem', 
                            'id' => '{_id}'
                        )              
                    )
                ),                             
            ),   
            array(            
                'name' => 'url_image',
                'type' => 'image',
                'title' => 'Photo',                                        
                'attributes' => array(
                    'src' => "{url_image}",
                    'width' => 25
                ),                     
            ),
            array(
                'name' => 'product_id',
                'title' => 'Product ID',                         
            ), 
            array(
                'name' => 'name',
                'title' => 'Product name',                             
            ),
            array(
                'name' => 'quantity',
                'type' => 'text',
                'title' => 'Quantity',
                'attributes' => array(
                    'name' => 'quantity[{product_id}]',
                    'value' => '{quantity}',
                    'class' => 'number ajax-change',
                    'data-product_id' => '{product_id}',
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/carts', 
                        array(
                            'action' => 'updateitems'
                        )
                    ),
                    'data-callback' => " 
                        $(\".total-quantity span\").html(result.totalQuantity);
                        $(\".total-money span\").html(result.totalMoney);
                        $.each(result.items, function( index, value ) {  
                            $(concat(\".cart-item-quantity-\", value.product_id)).html(value.quantity);
                            $(concat(\".cart-item-price-\", value.product_id)).html(value.price);
                            $(concat(\".cart-item-total-money-\", value.product_id)).html(value.total_money);
                        });
                    ",
                ),
            ), 
            array(
                'name' => 'price',
                'type' => 'text',
                'title' => 'Price',
                'attributes' => array(
                    'name' => 'price[{product_id}]',
                    'value' => '{price}',
                    'class' => 'number price ajax-change',
                    'data-a-sep' => ',',
                    'data-a-dec' => '.',
                    'data-v-max' => '99999999999',
                    'data-v-min' => '0',
                    'data-product_id' => '{product_id}',
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/carts', 
                        array(
                            'action' => 'updateitems'
                        )
                    ),
                    'data-callback' => " 
                        $(\".total-quantity span\").html(result.totalQuantity);
                        $(\".total-money span\").html(result.totalMoney);
                        $.each(result.items, function( index, value ) {                            
                            $(concat(\".cart-item-quantity-\", value.product_id)).html(value.quantity);
                            $(concat(\".cart-item-price-\", value.product_id)).html(value.price);
                            $(concat(\".cart-item-total-money-\", value.product_id)).html(value.total_money);
                        });
                    ",
                ),
            ),
            array(
                'name' => 'total_money',
                'type' => 'html',
                'title' => 'Money',
                'innerHtml' => '<span class="cart-item-total-money-{product_id}">{total_money}</span>',  
                'attributes' => array(
                ),
            ),                  
        );
    }

}