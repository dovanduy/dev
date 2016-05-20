<?php

namespace Admin\Model;

use Admin\Lib\Api;
use Application\Lib\Cache;

class Users {    
    
    public static function users_detail($id) {
        $key = USERS_DETAIL . $id;     
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_users_detail', array('user_id' => $id));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

}
