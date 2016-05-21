<?php

namespace Web\Model;

use Web\Lib\Api;
use Application\Lib\Arr;
use Application\Lib\Cache;

class LocaleStates {    
    
    public static function getAll($countryCode, $forDropdownList = true) {
        $key = STATES_ALL . $countryCode;     
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_locationstates_all', array('country_code' => $countryCode));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'iso', 
                'name'
            );
        }
        return $data;
    }

}
