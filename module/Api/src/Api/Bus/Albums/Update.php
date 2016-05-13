<?php

namespace Api\Bus\Albums;

use Api\Bus\AbstractBus;

class Update extends AbstractBus {

    protected $_required = array(
        'album_id',      
    );
    
    protected $_number_format = array(
        'album_id'
    );
    
    protected $_length = array(
        'album_id' => array(1, 11),
        'artist' => array(0, 100),
        'title' => array(0, 100),
        'user_id' => array(0, 11),
    );
    
    protected $_default_value = array(
        'user_id' => 0
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->updateAlbum($param);           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
