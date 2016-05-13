<?php

namespace Application\Model;

use Application\Lib\Auth;
use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

class Products
{
    public static function removeCache()
    {
        $key = PRODUCT_CATEGORIES_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = PRODUCT_CATEGORIES_ALL . $AppUI->website_id;
        }        
        Cache::remove($key);
    }
    
    public static function lastArrival()
    {
        $param = array();
        $key = PRODUCT_CATEGORIES_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = PRODUCT_CATEGORIES_ALL . $AppUI->website_id;
            $param['website_id'] = $AppUI->website_id;
        }
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_productcategories_all', $param);         
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if (!empty($categoryId)) {
            foreach ($data as $k => $d) {
                if ($d['category_id'] == $categoryId) {
                    unset($data[$k]);
                    break;
                }
            }
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'category_id', 
                'name'
            );
        }
        return $data;
    }   
    
}
