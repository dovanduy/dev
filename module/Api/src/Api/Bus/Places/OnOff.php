<?php

namespace Api\Bus\Places;

use Api\Bus\AbstractBus;

class OnOff extends AbstractBus {

    protected $_required = array(
        'id',
        'field',
        'value',
    );   
    
    protected $_number_format = array(
        'value'
    );
    
    protected $_length  = array(
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->updateOnOff($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }
    
}
