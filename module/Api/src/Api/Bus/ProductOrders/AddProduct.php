<?php

namespace Api\Bus\ProductOrders;

use Api\Bus\AbstractBus;

/**
 * Add a product to order
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AddProduct extends AbstractBus {
    
    protected $_required = array(
        'order_id',
        'product_id',
        'quantity'
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->addProduct($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
