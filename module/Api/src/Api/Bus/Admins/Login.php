<?php

namespace Api\Bus\Admins;

use Api\Bus\AbstractBus;

/**
 * Add categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class Login extends AbstractBus {
    
    protected $_required = array(
        'email',
        'password',       
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->login($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
