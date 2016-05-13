<?php

namespace Api\Bus\Festivals;

use Api\Bus\AbstractBus;

class Add extends AbstractBus {

    protected $_required = array(
        'state_code',
        'country_code',
        'name',
        'content'
    );
    
    protected $_number_format = array(
        'lat',
        'lng',
        'weekly',
        'regularly',
        'count_image',
    );

    protected $_date_format = array(
        'starts_at',
        'starts_time',
        'ends_at',
        'ends_time',
    );

    protected $_length = array(
        'street' => array(0, 100),
        'city_code' => array(0, 20),
        'state_code' => array(0, 20),
        'country_code' => array(2,2),
        'name' => array(0, 150),
        'tag' => array(0, 100),
        'short' => array(0, 255),
        'image_id' => array(0, 24),
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->save($param);
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
