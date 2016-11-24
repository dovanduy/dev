<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

class SaveAttribute extends AbstractBus {

    protected $_required = array(        
        'field',      
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->saveAttribute($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
