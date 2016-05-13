<?php

namespace Api\Bus\Festivals;

use Api\Bus\AbstractBus;

/**
 * Get list album
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Detail extends AbstractBus {

    protected $_required = array(
        'festival_id'
    );
    
    protected $_number_format = array(
        'festival_id'
    );
    
    protected $_length = array(
        'festival_id' => array(1, 11)
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
