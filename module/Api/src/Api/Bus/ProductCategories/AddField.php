<?php

namespace Api\Bus\ProductCategories;

use Api\Bus\AbstractBus;

/**
 * Add field
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AddField extends AbstractBus {
    
    protected $_required = array(
        'category_id',
        'field_id',
    );
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->addField($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
