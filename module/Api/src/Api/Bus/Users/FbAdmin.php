<?php

namespace Api\Bus\Users;

use Api\Bus\AbstractBus;

/**
 * update categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class FbAdmin extends AbstractBus {

    protected $_required = array(
             
    );
    
    public function operateDB($model, $param) {
        try { 
            $this->_response = $model->getFbAdmin($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
