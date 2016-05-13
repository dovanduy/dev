<?php

namespace Api\Bus\ProductOrders;

use Api\Bus\AbstractBus;

class SaveDetail extends AbstractBus {

    protected $_required = array(
        'order_id',
        'quantity',
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->saveDetail($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
