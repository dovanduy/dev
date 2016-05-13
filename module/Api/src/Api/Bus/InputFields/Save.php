<?php

namespace Api\Bus\InputFields;

use Api\Bus\AbstractBus;

class Save extends AbstractBus {

    protected $_required = array(
        'name',      
        'sort',      
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
