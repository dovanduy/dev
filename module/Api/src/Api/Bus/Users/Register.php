<?php

namespace Api\Bus\Users;

use Api\Bus\AbstractBus;

/**
 * Add categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Register extends AbstractBus {
    
    protected $_required = array(
        'website_id',
        'email',
        'password',
        'name',
        'mobile',        
        'country_code',
        'state_code',
        'city_code',
        'street',
        'address_name',
    );
	
	protected $_email_format = array(
        'email',
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->register($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
