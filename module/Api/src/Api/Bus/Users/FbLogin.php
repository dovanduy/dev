<?php

namespace Api\Bus\Users;

use Api\Bus\AbstractBus;

/**
 * update categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class FbLogin extends AbstractBus {

    protected $_required = array(
        'facebook_email',       
        'facebook_id',       
    );
	
	protected $_email_format = array(
        'facebook_email',
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->fbLogin($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
