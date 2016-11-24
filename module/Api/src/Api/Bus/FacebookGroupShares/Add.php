<?php

namespace Api\Bus\FacebookGroupShares;

use Api\Bus\AbstractBus;

/**
 * 
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
        'facebook_id',        
        'group_id',
        'wall_social_id',
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
