<?php

namespace Api\Bus\Hotels;

use Api\Bus\AbstractBus;

/**
 * Get list album
 *
 * @package 	Bus
 * @created 	2015-08-29
 * @version     1.0
 * @author      vinhls
 * @copyright   YouGo INC
 */
class Lists extends AbstractBus {

    protected $_required = array(
        //'id'
    );
    
    protected $_date_format = array(
        //'created_at' => 'Y-m-d'
    );
    
    protected $_length = array(
        //'id' => 1
    );
    
    protected $_default_value = array(
        //'id' => 1
    );
    
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
