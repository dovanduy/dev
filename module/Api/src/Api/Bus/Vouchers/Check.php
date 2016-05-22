<?php

namespace Api\Bus\Vouchers;

use Api\Bus\AbstractBus;

/**
 * Check voucher
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Check extends AbstractBus {
    
    protected $_required = array(   
        'voucher_code'
    );    
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->check($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
