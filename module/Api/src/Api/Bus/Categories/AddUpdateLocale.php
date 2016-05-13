<?php

namespace Api\Bus\Categories;

use Api\Bus\AbstractBus;
class AddUpdateLocale extends AbstractBus {

    protected $_required = array(
        '_id',
        'name',
        'short',
        'content',
    );
    
    protected $_number_format = array(
        'place_id',
        'is_locale' 
    );
    
    protected $_length  =  array(
        'place_id'      => array(0, 11),
        'is_locale'     => array(0, 11),
        'locale'        => 2,
        'name'          => array(1, 150),
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
