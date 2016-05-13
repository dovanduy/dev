<?php

namespace Api\Bus\ProductCategories;

use Api\Bus\AbstractBus;

/**
 * Get filter for brands/price by categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Filter extends AbstractBus {
    
    protected $_required = array( 
        //'category_id',
    );
    
    protected $_number_format = array(
        
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->filter($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
