<?php

namespace Api\Bus\ProductCategories;

use Api\Bus\AbstractBus;

/**
 * Add a product to category
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AddProduct extends AbstractBus {
    
    protected $_required = array(
        'category_id',
        'product_id',
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