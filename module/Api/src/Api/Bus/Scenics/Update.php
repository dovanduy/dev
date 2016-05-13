<?php

namespace Api\Bus\Scenics;

use Api\Bus\AbstractBus;
use Exception;
class Update extends AbstractBus {

    protected $_required = array(
        'scenic_id'
    );
    
    protected $_number_format = array(
        'lat',
        'lng',
        'is_locale',
        'compass',
        'distance_center'
    );
    
    protected $_length = array(
        'street'       => array(0, 100),
        'city_code'    => array(0, 20),
        'state_code'   => array(0, 20),
        'country_code' => array(2),
        'image_id'     => array(24)        
    );
    
    protected $_default_value = array(
        //'id' => 1
    );
    
    public function operateDB( $model, $param ) 
    {
        try 
        {
            $this->_response = $model->updateInfo($param);
            return $this->result($model->error());
        } 
        catch (Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }
}
