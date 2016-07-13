<?php

namespace Api\Bus\Images;

use Api\Bus\AbstractBus;

class Update extends AbstractBus {
    
    protected $_required = array(       
        'id',            
        'src',            
    );   
    
    protected $_length  = array(
       
    );
    
    protected $_default_value  = array(
        
    );
    
    protected $_url_format = array(
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
