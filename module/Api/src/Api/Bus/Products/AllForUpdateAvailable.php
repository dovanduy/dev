<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * Get AllForUpdateAvailable
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AllForUpdateAvailable extends AbstractBus {
    
    protected $_required = array( 
        'website_id'
    );
    
    protected $_number_format = array(
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->getForUpdateAvailable($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
