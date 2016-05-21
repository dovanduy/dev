<?php

namespace Api\Bus\Users;

use Api\Bus\AbstractBus;

/**
 * update new password
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class UpdateNewPassword extends AbstractBus {

    protected $_required = array(
        'token',       
        'password',       
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->updateNewPassword($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
