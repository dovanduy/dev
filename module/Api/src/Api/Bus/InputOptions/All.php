<?php

namespace Api\Bus\InputOptions;

use Api\Bus\AbstractBus;

/**
 * Get all options
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
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
