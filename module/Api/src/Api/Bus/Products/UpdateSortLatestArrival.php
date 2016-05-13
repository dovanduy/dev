<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

class UpdateSortLatestArrival extends AbstractBus {

    protected $_required = array(
        'sort',      
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->updateSortLatestArrival($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
