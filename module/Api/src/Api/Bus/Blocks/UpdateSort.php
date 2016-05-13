<?php

namespace Api\Bus\Blocks;

use Api\Bus\AbstractBus;

class UpdateSort extends AbstractBus {

    protected $_required = array(
        'sort',      
    );
    
    public function operateDB($model, $param) {
        try {            
            $this->_response = $model->updateSort($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
