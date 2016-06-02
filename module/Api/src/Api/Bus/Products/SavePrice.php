<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

class SavePrice extends AbstractBus {

    protected $_required = array(
        'product_id',
        'price',
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->savePrice($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
