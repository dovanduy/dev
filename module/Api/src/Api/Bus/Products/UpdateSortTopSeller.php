<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

class UpdateSortTopSeller extends AbstractBus {

    protected $_required = array(
        'sort',      
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->updateSortTopSeller($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
