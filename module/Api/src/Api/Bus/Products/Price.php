<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * Get price
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Price extends AbstractBus {
    
    protected $_required = array(  
        'website_id',
        'product_id',
        'color_id',
        'size_id',	
    );
    
    protected $_number_format = array(
        'website_id',
        'product_id',
        'color_id',
        'size_id',
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->getPrice($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
