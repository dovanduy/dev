<?php

namespace Api\Bus\Pages;

use Api\Bus\AbstractBus;

class AddUpdateLocale extends AbstractBus {

    protected $_required = array(
        '_id',
        'title',
        'locale'  
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->addUpdateLocale($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
