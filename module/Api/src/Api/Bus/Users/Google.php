<?php

namespace Api\Bus\Users;

use Api\Bus\AbstractBus;

/**
 * Get all users
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Google extends AbstractBus {
    
    protected $_required = array(       
		'email'
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->google($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
