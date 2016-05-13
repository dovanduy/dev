<?php

namespace Api\Bus\Places;

use Api\Bus\AbstractBus;

/**
 * Get list places
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class Lists extends AbstractBus {
    
    protected $_required = array(       
        'limit',
        'page',        
    );
    
    protected $_number_format = array(
        'limit',
        'page'
    );
    
    protected $_default_value  = array(
        'page' => 1,
        'role' => 1,
        'status' => 1
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
