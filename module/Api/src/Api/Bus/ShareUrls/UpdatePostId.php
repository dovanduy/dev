<?php

namespace Api\Bus\ShareUrls;

use Api\Bus\AbstractBus;

/**
 * Add categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class UpdatePostId extends AbstractBus {
    
    protected $_required = array(
        'values'
    );
    
    public function operateDB($sm, $param) {
        try {
            $model = $sm->get('SharePostIds');  
            $this->_response = $model->addUpdate($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
