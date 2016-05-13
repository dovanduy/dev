<?php

namespace Web\Model;

use Application\Lib\Auth;
use Application\Lib\Cache;
use Application\Lib\Arr;
use Web\Module as WebModule;
use Web\Lib\Api;

class Brands
{
    public static function removeCache()
    {
        $websiteId = WebModule::getConfig('website_id');
        Cache::remove(BRAND_ALL . $websiteId);        
    }
    
    public static function getAll($featured = null)
    {
        $key = BRAND_ALL . WebModule::getConfig('website_id');
        if (!($result = Cache::get($key))) {
            $param = array();
            $result = Api::call('url_brands_all', $param);         
            if (!empty($result)) {
                Cache::set($key, $result);
            }
        }        
        if ($featured == true) {
            $result = Arr::filter($result, 'featured', 1);
        }
        return $result;
    } 
    
    public static function getFilter($categoryId)
    {
        $websiteId = WebModule::getConfig('website_id');
        $param = array();
        $key = PRODUCT_CATEGORIES_FILTER . $websiteId . md5($categoryId);
        $param['category_id'] = $categoryId;       
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_productcategories_filter', $param);        
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }        
        return $data;
    }
}
