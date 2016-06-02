<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * Set display priority for product
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class SetPriority extends AbstractBus {
    
    protected $_required = array(
        'product_id',
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->setPriority($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
