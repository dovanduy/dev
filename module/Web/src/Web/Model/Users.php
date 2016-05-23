<?php

namespace Web\Model;

use Web\Lib\Api;
use Application\Lib\Cache;

class Users {    
    
    public static function removeCache($_id)
    {
        $key = USERS_DETAIL . $_id;
        Cache::remove($key);
    }
    
    public static function getDetail($_id) {
        $key = USERS_DETAIL . $_id;     
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_users_detail', array('_id' => $_id));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

}
