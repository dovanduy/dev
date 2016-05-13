<?php

namespace Api\Bus\Hotels;

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
class Detail extends AbstractBus {

    protected $_required = array(
        'hotel_id'
    );
    
    protected $_number_format = array(
        'hotel_id'
    );
    
    protected $_length = array(
        'hotel_id' => array(1, 11)
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->getDetail($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
