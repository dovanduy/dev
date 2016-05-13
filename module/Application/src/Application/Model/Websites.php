<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

class Websites
{

    public static function removeCache()
    {
        $key = WEBSITES_ALL;        
        Cache::remove($key);
    }
    
    public static function getAll($websiteId = null, $forDropdownList = true)
    {
        $param = array();
        $key = WEBSITES_ALL;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_websites_all', $param);
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if (!empty($websiteId)) {
            foreach ($data as $k => $d) {
                if ($d['website_id'] == $websiteId) {
                    unset($data[$k]);
                    break;
                }
            }
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'website_id', 
                'url'
            );
        }
        return $data;
    }
    
}
