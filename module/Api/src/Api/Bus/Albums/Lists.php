<?php

namespace Api\Bus\Albums;

use Api\Bus\AbstractBus;

/**
 * Get list album
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class Lists extends AbstractBus {
    
    public function operateDB($model, $param) {
        try {            
            $this->_response = $model->getList($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
