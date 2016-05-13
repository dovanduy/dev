<?php

namespace Api\Bus\Places;

use Api\Bus\AbstractBus;

class Add extends AbstractBus {
    
    protected $_required = array(       
        'login_admin_id', 
        'country_code', 
        'state_code',
        'name',
        'tag',
        'short',
        'content',
    );   
    
    protected $_length  = array(
        'country_code' => 2,
        'state_code' => array(2, 20),
        'lat' => array(0, 11),
        'lng' => array(0, 11),
        'url_website' => array(0, 255),
        'name' => array(1, 150),
        'tag' => array(1, 100),
        'short' => array(1, 255),
        'image_id' => array(0, 11)
    );
    
    protected $_default_value  = array(
        'login_admin_id' => 0,
        'lat' => 0,
        'lng' => 0,
        'expired_at' => null
    );
    
    protected $_url_format = array(
        'url_website',
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
