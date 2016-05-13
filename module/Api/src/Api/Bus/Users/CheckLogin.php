<?php

namespace Api\Bus\Users;

use Api\Bus\AbstractBus;

/**
 * Add user
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class CheckLogin extends AbstractBus {
    
    protected $_required = array(
        'email',
        'password',       
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->checkLogin($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}