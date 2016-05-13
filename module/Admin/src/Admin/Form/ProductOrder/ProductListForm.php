<?php

namespace Admin\Form\ProductOrder;

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
class ProductListForm extends AbstractForm
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
                    'value' => $this->translate('Add Product'),
                    'class' => 'btn btn-primary show-model',
                    'data-modelid' => '#add-product-modal',
                ),
            )
        );
    }

    /**
     * Html will embedded after table
     *
     * @return array Array to create columns for table
     */
    public function tfoot()
    {
        $products = $this->getDataset();
        $totalMoney = 0;
        if (!empty($products)) {            
            foreach ($products as &$product) {                
                if (!empty($product['active'])) {
                    $totalMoney += db_float($product['total_money']);                    
                }
            }
        }
        return '
            <tr>
                <td colspan="7" align="right">
                    <div class="total-money">' . $this->translate('Total') . ':' . 
                    '<span>' . money_format($totalMoney) . '</span></div>
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
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/productorders', 
                        array(
                            'action' => 'removeproduct'
                        ),
                        array(
                            'query' => array(
                                'order_id' => '{order_id}',                                
                                'product_id' => '{product_id}',                                
                            )
                        )
                    ), 
                    'data-confirmmessage' => $this->translate('Are you sure?'),
                    'data-beforesubmit' => "                         
                        var tr = btn.closest(\"tr\"); 
                        tr.remove(); 
                    ",
                    'data-callback' => " 
                        $(\"#order-detail .total-money span\").html(result.totalMoney);
                    ",
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
                'name' => 'product_name',
                'title' => 'Product name',                             
            ),
            array(
                'name' => 'quantity',
                'type' => 'text',
                'title' => 'Quantity',
                'attributes' => array(
                    'name' => 'quantity[{id}]',
                    'value' => '{quantity}',
                    'class' => 'number ajax-change',
                    'data-id' => '{id}',
                    'data-product_id' => '{product_id}',
                    'data-a-sep' => ',',
                    'data-a-dec' => '.',
                    'data-v-max' => '99999999999',
                    'data-v-min' => '0',
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/productorders', 
                        array(
                            'action' => 'savedetail'
                        ),
                        array(
                            'query' => array(
                                'order_id' => '{order_id}'
                            )
                        )
                    ),
                    'data-beforesend' => " 
                        var id = inp.data(\"id\");
                        var quantity = db_int(inp.val());    
                        $(\".td_price .price\").each(function() {
                            var pId = $(this).data(\"id\");
                            if (pId == id) {
                                var money = money_format(db_float($(this).val()) * quantity);
                                $(concat(\".total-money-\",id)).html(money);
                            }
                        });
                    ",
                    'data-callback' => " 
                        $(\"#order-detail .total-money span\").html(result.totalMoney);
                    ",
                ),
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
                        'admin/productorders', 
                        array(
                            'action' => 'savedetail'
                        ),
                        array(
                            'query' => array(
                                'order_id' => '{order_id}'
                            )
                        )
                    ),
                    'data-beforesend' => " 
                        var id = inp.data(\"id\");
                        var price = db_int(inp.val());    
                        $(\".td_quantity .number\").each(function() {
                            var qId = $(this).data(\"id\");
                            if (qId == id) {
                                var money = money_format(db_float($(this).val()) * price);
                                $(concat(\".total-money-\",id)).html(money);
                            }
                        });
                    ",
                    'data-callback' => " 
                        $(\"#order-detail .total-money span\").html(result.totalMoney);
                    ",
                ),
            ),
            array(
                'name' => 'total_money',
                'type' => 'html',
                'title' => 'Subtotal',
                'innerHtml' => '<span class="total-money-{id}">{total_money}</span>',  
                'attributes' => array(
                ),
            ),                   
        );
    }

}