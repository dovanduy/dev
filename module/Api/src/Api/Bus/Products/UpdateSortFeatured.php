<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

class UpdateSortFeatured extends AbstractBus {

    protected $_required = array(
        'sort',      
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->updateSortFeatured($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
