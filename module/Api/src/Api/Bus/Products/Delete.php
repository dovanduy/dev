<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

class Delete extends AbstractBus {

    protected $_required = array(
        'website_id',      
        'product_id',      
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->deleteProduct($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
