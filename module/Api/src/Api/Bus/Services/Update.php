<?php

namespace Api\Bus\Services;

use Api\Bus\AbstractBus;

class Update extends AbstractBus {

    protected $_required = array(
        'service_id'
    );
    
    protected $_number_format = array(
        'is_locale',
        'iseq',
        'parent_id'
    );
    
    protected $_length = array(
        'is_locale' => array(1, 11),
        'tag' => array(0, 20),
        'type' => array(0, 20),
        'iseq' => array(1, 11),
        'parent_id' => array(1, 11),
        'name' => array(1, 50),
        'values' => array(1, 2000)
    );

    public function operateDB($model, $param) {
        try {
            $this->_response = $model->save($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }
}
