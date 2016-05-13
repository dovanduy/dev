<?php

namespace Api\Bus\LocationCountries;

use Api\Bus\AbstractBus;

/**
 * Get all country
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class All extends AbstractBus {
    
    protected $_required = array(       
            
    );
    
    protected $_number_format = array(
        
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->getAll($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
