<?php

namespace Api\Bus\WebsiteCategories;

use Api\Bus\AbstractBus;

/**
 * Get detail categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Detail extends AbstractBus {
    
    protected $_required = array(       
    );
    
    protected $_number_format = array(
        'parent_id',
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->getDetail($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
