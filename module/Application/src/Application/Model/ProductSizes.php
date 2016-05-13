<?php

namespace Application\Model;

use Application\Lib\Auth;
use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

class ProductSizes
{
    public static function removeCache()
    {
        $key = PRODUCT_SIZES_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = PRODUCT_SIZES_ALL . $AppUI->website_id;
        }        
        Cache::remove($key);
    }
    
    public static function getAll($forDropdownList = true)
    {
        $param = array();
        $key = PRODUCT_SIZES_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = PRODUCT_SIZES_ALL . $AppUI->website_id;
            $param['website_id'] = $AppUI->website_id;
        }
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_productsizes_all', $param);         
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }        
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'size_id', 
                'short'
            );
        }
        return $data;
    }    
    
}
