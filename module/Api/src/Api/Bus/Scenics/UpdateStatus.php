<?php

namespace Api\Bus\Scenics;

use Api\Bus\AbstractBus;
use Exception;
class UpdateStatus extends AbstractBus {

    protected $_required = array(
        'scennic_id'
    );   
    
    protected $_number_format = array(
        'status'
    );
    
    protected $_length  = array(
        'status'        => array(0, 4),
        'scennic_id'    => array(0, 24),
    );

    protected $_default_value = array(
    );

    public function operateDB($model, $param) {
        try {
            $this->_response = $model->updateStatus($param);           
            return $this->result($model->error());
        } 
        catch (Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
