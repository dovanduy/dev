<?php

namespace Api\Bus\Banners;

use Api\Bus\AbstractBus;

/**
 * update banners
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Update extends AbstractBus {

    protected $_required = array(
        '_id',
        'url',
    );
    
    protected $_url_format = array(
        'url',
    );
     
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->updateInfo($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
