<?php

namespace Api\Bus\Albums;

use Api\Bus\AbstractBus;

class Delete extends AbstractBus {

    protected $_required = array(
        'album_id',      
    );
    
    protected $_date_format = array(
        //'title' => 'Y-m-d'
    );
    
    protected $_number_format = array(
        'album_id'
    );
    
    protected $_length = array(
        'album_id' => array(1, 11),       
    );
    
    public function operateDB($model, $param) {
        try {
            $this->_response = $model->deleteAlbum(
                array(
                    'where' => array('album_id' => $param['album_id'])
                )                 
            );           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }
    
}
