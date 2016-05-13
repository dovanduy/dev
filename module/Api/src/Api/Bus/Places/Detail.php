<?php

namespace Api\Bus\Places;

use Api\Bus\AbstractBus;

class Detail extends AbstractBus {

    protected $_required = array(
        '_id',
        'locale'
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->getDetail($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
