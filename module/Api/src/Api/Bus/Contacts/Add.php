<?php

namespace Api\Bus\Contacts;

use Api\Bus\AbstractBus;

/**
 * Add contact
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Add extends AbstractBus {
    
    protected $_required = array(
        'email',
        'name',
        'subject',
        'content',        
        'website_id',
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->add($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
