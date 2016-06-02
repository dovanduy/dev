<?php

namespace Api\Bus\ProductCategories;

use Api\Bus\AbstractBus;

/**
 * Allow filter field
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AllowFilterField extends AbstractBus {
    
    protected $_required = array(
        'category_id',
        'field_id',
        'value',
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->updateAllowFilter($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
