<?php

namespace Api\Bus\Validator;

use Api\Bus\AbstractBus;

/**
 * Validate duplicate email
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class DuplicateEmail extends AbstractBus {

    protected $_required = array(
        'email'
    );
    
    protected $_email_format = array(
        'email'
    );
    
    public function operateDB($model, $param) {
        try {            
            $this->_response = $model->duplicateEmail($param);       
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
