<?php

namespace Admin\Model;

use Admin\Lib\Api;
use Application\Lib\Cache;

class Services {
    
    public static function services_detail($id, $locale) {
        $key = SERVICES_DETAIL . $id;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_services_detail', array('service_id' => $id, 'locale' => $locale));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

}
