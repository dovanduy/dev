<?php

namespace Application\Model;

use Application\Lib\Auth;
use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

class ProductColors
{
    public static function removeCache()
    {
        $key = PRODUCT_COLORS_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = PRODUCT_COLORS_ALL . $AppUI->website_id;
        }        
        Cache::remove($key);
    }
    
    public static function getAll($forDropdownList = true, $productId = 0)
    {
        $param = array();
        $key = PRODUCT_COLORS_ALL;
        $auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = PRODUCT_COLORS_ALL . $AppUI->website_id . '_' . $productId;
            $param['website_id'] = $AppUI->website_id;            
        }
        $param['product_id'] = $productId;
        if (!($data = Cache::get($key)) || 1) {
            $data = Api::call('url_productcolors_all', $param); 
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }        
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'color_id', 
                'name'
            );
        }
        return $data;
    }    
    
}
