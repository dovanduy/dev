<?php

namespace Api\Bus\Images;

use Api\Bus\AbstractBus;

class Detail extends AbstractBus {

    protected $_required = array(
        'id',
        'src'
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
