<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;
use Application\Lib\Auth;

class Brands
{

    public static function removeCache()
    {
        $key = BRAND_CATEGORIES_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = BRAND_CATEGORIES_ALL . $AppUI->website_id;
        }        
        Cache::remove($key);
    }
    
    public static function getAll($brandId = null, $forDropdownList = true)
    {
        $param = array();
        $key = BRAND_CATEGORIES_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = BRAND_CATEGORIES_ALL . $AppUI->website_id;
            $param['website_id'] = $AppUI->website_id;
        }        
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_brands_all', $param);
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if (!empty($brandId)) {
            foreach ($data as $k => $d) {
                if ($d['brand_id'] == $brandId) {
                    unset($data[$k]);
                    break;
                }
            }
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'brand_id', 
                'name'
            );
        }
        return $data;
    }
    
}
