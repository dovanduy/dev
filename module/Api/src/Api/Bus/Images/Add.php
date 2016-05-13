<?php

namespace Api\Bus\Images;

use Api\Bus\AbstractBus;

class Add extends AbstractBus {
    
    protected $_required = array(       
        'src', 
        'src_id'        
    );   
    
    protected $_length  = array(
       
    );
    
    protected $_default_value  = array(
        'is_main' => 0
    );
    
    protected $_url_format = array(
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->multiAdd($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
