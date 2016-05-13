<?php

namespace Api\Bus\ProductCategories;

use Api\Bus\AbstractBus;

/**
 * Get all brands by categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AllBrand extends AbstractBus {
    
    protected $_required = array( 
        'category_id',
    );
    
    protected $_number_format = array(
        
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->getAllBrands($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
