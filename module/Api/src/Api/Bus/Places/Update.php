<?php

namespace Api\Bus\Places;

use Api\Bus\AbstractBus;

class Update extends AbstractBus {

    protected $_required = array(
        'login_admin_id',
        '_id',
    );   
    
    protected $_date_format = array(
        'expired_at'
    );
    
    protected $_default_value  = array(
        'login_admin_id' => 0,
        'lat' => 0,
        'lng' => 0,
        'expired_at' => null
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
