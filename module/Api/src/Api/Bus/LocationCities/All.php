<?php

namespace Api\Bus\LocationCities;

use Api\Bus\AbstractBus;

/**
 * Get list cities
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class All extends AbstractBus {
    
    protected $_required = array(       
        'country_code',
        'state_code',
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
