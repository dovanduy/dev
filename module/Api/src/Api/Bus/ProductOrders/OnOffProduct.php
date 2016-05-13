<?php

namespace Api\Bus\ProductOrders;

use Api\Bus\AbstractBus;

class OnOffProduct extends AbstractBus {

    protected $_required = array(
        'order_id',       
        '_id',  // product_id     
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
            $this->_response = $model->onOffProduct($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }
    
}