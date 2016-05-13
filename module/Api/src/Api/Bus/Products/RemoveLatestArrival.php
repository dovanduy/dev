<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * Remove a top seller product
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class RemoveLatestArrival extends AbstractBus {
    
    protected $_required = array(
        'product_id'
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->removeLatestArrival($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
