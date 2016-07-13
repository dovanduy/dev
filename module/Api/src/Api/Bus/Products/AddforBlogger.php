<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * Add a product to featured list
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class AddForBlogger extends AbstractBus {
    
    protected $_required = array(
        'category_id'
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->getForBlogger($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
