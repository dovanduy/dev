<?php

namespace Api\Bus\Scenics;

use Api\Bus\AbstractBus;
use Exception;
class Detail extends AbstractBus 
{
    protected $_required = array(
        '_id',
        'locale',
    );
    
    protected $_length = array(
        '_id'    => array(24),
        'locale' => array(2),
    );
    protected $_default_value  = array(
        'locale' => '',
    );
    public function operateDB( $model, $param ) 
    {
        try 
        {
            $this->_response = $model->getDetail( $param );
            return $this->result($model->error());
        } 
        catch (Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
