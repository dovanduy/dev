<?php

namespace Api\Bus\Pages;

use Api\Bus\AbstractBus;

class OnOff extends AbstractBus {

    protected $_required = array(
        '_id',       
        'value',
    );   
    
    protected $_number_format = array(
        'value'
    ); 
    
    protected $_default_value = array(
        'field' => 'active'
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
