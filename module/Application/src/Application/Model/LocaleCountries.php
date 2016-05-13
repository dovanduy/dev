<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Arr;
use Application\Lib\Cache;

class LocaleCountries {    
    
    public static function getAll($forDropdownList = true) {
        $key = COUNTRIES_ALL;     
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_locationcountries_all');
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
               'iso_a2', 
                'name'
            );
        }
        return $data;
    }

}
