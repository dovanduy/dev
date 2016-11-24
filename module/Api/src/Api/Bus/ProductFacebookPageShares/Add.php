<?php

namespace Api\Bus\ProductFacebookPageShares;

use Api\Bus\AbstractBus;

/**
 * Add banners
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Add extends AbstractBus {
    
    protected $_required = array(
        'user_id',        
        'product_id',
        'social_id',
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
