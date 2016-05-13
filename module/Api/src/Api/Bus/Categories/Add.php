<?php

namespace Api\Bus\Categories;

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
class Add extends AbstractBus {
    
    protected $_required = array(
        'name',
        'short',
        'content',
        'iseq',
    );
    
    protected $_number_format = array(
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->Add($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
