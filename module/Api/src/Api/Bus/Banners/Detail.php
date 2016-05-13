<?php

namespace Api\Bus\Banners;

use Api\Bus\AbstractBus;

/**
 * Get detail banners
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
