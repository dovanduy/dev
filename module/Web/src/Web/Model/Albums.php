<?php

namespace Web\Model;

use Application\Lib\Api;
use Application\Lib\Cache;

class Albums {    
    
    public static function albums_detail($id) {
        $key = ALBUMS_DETAIL . $id;     
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_albums_detail', array('album_id' => $id));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

}
