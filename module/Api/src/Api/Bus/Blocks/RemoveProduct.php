<?php

namespace Api\Bus\Blocks;

use Api\Bus\AbstractBus;

/**
 * Add a product to block
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class RemoveProduct extends AbstractBus {
    
    protected $_required = array(
        'block_id',
        'product_id',
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->remove($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
