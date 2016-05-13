<?php

namespace Api\Bus\Websites;

use Api\Bus\AbstractBus;

/**
 * Get list categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Lists extends AbstractBus {
    
    protected $_required = array(       
    );
    
    protected $_number_format = array(
        'limit',
        'page'
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
