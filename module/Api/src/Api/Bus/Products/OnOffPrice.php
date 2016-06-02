<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

class OnOffPrice extends AbstractBus {

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
            $this->_response = $model->updateOnOffPrice($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }
    
}