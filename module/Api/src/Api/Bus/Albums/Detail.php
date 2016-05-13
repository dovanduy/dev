<?php

namespace Api\Bus\Albums;

use Api\Bus\AbstractBus;

/**
 * Get list album
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class Detail extends AbstractBus {

    protected $_required = array(
        'album_id'
    );
    
    protected $_number_format = array(
        'album_id'
    );
    
    protected $_length = array(
        'album_id' => array(1, 11)
    );
    
    public function operateDB($model, $param) {
        try {            
            $this->_response = $model->find(
                array(
                    'where' => array('album_id' => $param['album_id'])
                ), 
                $model::RETURN_TYPE_ONE
            );           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
