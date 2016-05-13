<?php

namespace Api\Bus\Festivals;

use Api\Bus\AbstractBus;
class AddUpdateLocale extends AbstractBus {

    protected $_required = array(
        '_id',
        'locale',
        'name',
        'content',
    );
    
    protected $_number_format = array(
    );
    
    protected $_length  =  array(
        '_id'      => array(0, 24),
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
