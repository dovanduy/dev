<?php

namespace Api\Bus\Places;

use Api\Bus\AbstractBus;

class UpdateStatus extends AbstractBus {

    protected $_required = array(

    );   
    
    protected $_number_format = array(
        'status'
    );
    
    protected $_length  = array(
        'status'        => array(0, 4),
        '_id'            => array(0, 24),
    );

    protected $_default_value = array(
    );

    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->updateStatus($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
