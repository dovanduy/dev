<?php

namespace Api\Bus\Scenics;

use Api\Bus\AbstractBus;
use Exception;
use Application\Lib\Log;
class Lists extends AbstractBus 
{    
    protected $_required = array(       
        'limit',
        'page',      
    );
    
    protected $_number_format = array(
        'limit',
        'page'
    );
    
    protected $_default_value  = array(
        'page' => 1,
        'limit'=> 10,
    );
    public function operateDB( $model, $param )
    {
        try 
        {
            $this->_response = $model->getList( $param );
            Log::info($this->_response);
            return $this->result( $model->error() );
        } 
        catch (Exception $e) {
        	Log::error($e);
            $this->_exception = $e;
        }
        return false;
    }

}
