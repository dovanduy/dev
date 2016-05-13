<?php

namespace Api\Bus\Albums;

use Api\Bus\AbstractBus;

class UpdateIseq extends AbstractBus {

    protected $_required = array(
        'iseq',      
    );
    
    public function operateDB($model, $param) {
        try {            
            $this->_response = $model->updateIseq($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
