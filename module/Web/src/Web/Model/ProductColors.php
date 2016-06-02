<?php

namespace Web\Model;

use Application\Lib\Cache;
use Application\Lib\Arr;

use Web\Module as WebModule;
use Web\Lib\Api;

class ProductColors
{
    public static function removeCache()
    {
        $key = PRODUCT_COLORS_ALL .  WebModule::getConfig('website_id');    
        Cache::remove($key);
    }
    
    public static function getAll($productId = 0, $forDropdownList = true)
    {
        $param = array();
        $key = PRODUCT_COLORS_ALL .  WebModule::getConfig('website_id') . '_' . $productId;       
        $param['product_id'] = $productId;
        if (!($data = Cache::get($key))) {
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
