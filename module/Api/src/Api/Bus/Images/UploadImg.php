<?php

namespace Api\Bus\Images;

use Api\Bus\AbstractBus;

class UploadImg extends AbstractBus {
    
    protected $_required = array(       
        'url_image'       
    );   
    
    protected $_length  = array(
       
    );
    
    protected $_default_value  = array(
        
    );
    
    protected $_url_format = array(
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->upload($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
