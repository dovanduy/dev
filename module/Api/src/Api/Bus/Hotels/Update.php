<?php

namespace Api\Bus\Hotels;

use Api\Bus\AbstractBus;

class Update extends AbstractBus {

    protected $_required = array(
        'hotel_id',
        'category_id',
        'star',
        'street',
        'city_code',
        'state_code',
        'country_code',
        'tel',
        'fax',
        'url_website',
        'hotline',
        'email',
        'co_read',
        'co_like',
        'co_comment',
        'co_rated',
        'co_rated_person',
        'last_comment_id',
        'is_locale',
        'lat',
        'lng',
        'is_verified'
    );
    
    protected $_date_format = array(
        'expires_at' => 'Y-m-d'
    );
    
    protected $_number_format = array(
        'hotel_id',
        'category_id',
        'star',
        'image_id',
        'co_read',
        'co_like',
        'co_comment',
        'co_rated',
        'co_rated_person',
        'last_comment_id',
        'is_locale',
        'lat',
        'lng',
        'is_verified'
    );
    
    protected $_length = array(
        'hotel_id' => array(1, 11),
        'category_id' => array(1, 11),
        'star' => array(0, 4),
        'street' => array(1, 100),
        'city_code' => array(1, 24),
        'state_code' => array(1, 24),
        'country_code' => array(1, 2),
        'tel' => array(1, 30),
        'fax' => array(1, 30),
        'url_website' => array(1, 255),
        'hotline' => array(1, 30),
        'email' => array(1, 255),
        'image_id' => array(0, 11),
        'co_read' => array(1, 11),
        'co_like' => array(1, 11),
        'co_comment' => array(1, 11),
        'co_rated' => array(1, 11),
        'co_rated_person' => array(1, 11),
        'last_comment_id' => array(1, 11),
        'is_locale' => array(1, 11),
        'lat' => array(1, 18),
        'lng' => array(1, 18),
        'is_verified' => array(1, 4)
    );
    
    protected $_default_value = array(
        //'id' => 1
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
