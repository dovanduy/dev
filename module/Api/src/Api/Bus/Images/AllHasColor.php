<?php

namespace Api\Bus\Images;

use Api\Bus\AbstractBus;

/**
 * Get all images
 *
 * @package 	Bus
 * @created 	2015-09-20
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AllHasColor extends AbstractBus {
    
    protected $_required = array(       
        'src_id',
        'src',        
    );    
    
    public function operateDB($model, $param) { 
        try {          
            $this->_response = $model->getAllHasColor($param);         
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
