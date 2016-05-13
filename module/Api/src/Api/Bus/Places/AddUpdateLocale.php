<?php

namespace Api\Bus\Places;

use Api\Bus\AbstractBus;

class AddUpdateLocale extends AbstractBus {

    protected $_required = array(       
        '_id', 
        'locale',
        'name',
        'tag',
        'short',
        'content',
        'content_mobile',
    ); 
    
    protected $_length  =  array(
        'locale'        => 2,
        'name'          => array(1, 150),
        'tag'           => array(1, 100),
        'short'         => array(1, 255),
    );
    
    protected $_default_value = array(
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->addUpdateLocale($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
