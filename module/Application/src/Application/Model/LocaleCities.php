<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Arr;
use Application\Lib\Cache;

class LocaleCities {    
    
    public static function getAll($stateCode, $countryCode = '', $forDropdownList = true) {
        $key = CITIES_ALL . $stateCode . '_' . $countryCode;     
        if (!($data = Cache::get($key)) || 1) {
            $data = Api::call('url_locationcities_all', array(
                'state_code' => $stateCode,
                'country_code' => $countryCode
            ));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'code', 
                'name'
            );
        }
        return $data;
    }

}
