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
class ListsCounty extends AbstractBus {
    
    protected $_required = array(  
        'country_code',
        'is_locale',
        'locale',
        'limit',
        'page',        
    );
    
    protected $_number_format = array(
        'is_locale',
        'limit',
        'page'
    );
    
    public function operateDB($model, $param) { 
        try {          
            $this->_response = $model->getListCountry($param);         
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
