<?php

namespace Api\Bus\Albums;

use Api\Bus\AbstractBus;

class Add extends AbstractBus {

    protected $_required = array(       
        'artist',
        'title',        
    );   
    
    protected $_number_format = array(
        'user_id'
    );
    
    protected $_length = array(
        'user_id' => array(1, 11),
        'artist' => array(1, 100),
        'title' => array(1, 100),
    );
    
    protected $_default_value = array(
        'user_id' => 0
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->add($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
