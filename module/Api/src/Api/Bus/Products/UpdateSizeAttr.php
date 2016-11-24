<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * update categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class UpdateSizeAttr extends AbstractBus {

    protected $_required = array(
        'product_id',
        'value',
    );
    
    public function operateDB($model, $param) {
        try {          
            $param['value'] = trim($param['value']);
            $this->_response = $model->updateSizeAttr($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
