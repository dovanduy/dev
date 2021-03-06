<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * Add categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AddPrice extends AbstractBus {
    
    protected $_required = array(
        'website_id',
        'product_id'
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->addPrice($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
