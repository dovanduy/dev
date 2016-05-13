<?php

namespace Admin\Model;

use Application\Lib\Api;
use Application\Lib\Cache;

class Hotels {
    
    public static function hotels_detail($id, $locale) {
        $key = HOTELS_DETAIL . $id;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_hotels_detail', array('hotel_id' => $id, 'locale' => $locale));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

}
