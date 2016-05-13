<?php

namespace Web\Model;

use Application\Lib\Auth;
use Application\Lib\Cache;
use Application\Lib\Arr;
use Web\Module as WebModule;
use Web\Lib\Api;

class Products
{
    public static function removeCache()
    {
        $websiteId = WebModule::getConfig('website_id');
        Cache::remove(PRODUCT_LAST_ARRIVAL . $websiteId);
        Cache::remove(PRODUCT_FEATURED . $websiteId);
        Cache::remove(PRODUCT_TOP_SELLER . $websiteId);
    }
    
    public static function lastArrival()
    {
        $param = array();
        $key = PRODUCT_LAST_ARRIVAL . WebModule::getConfig('website_id');
        if (!($result = Cache::get($key))) {
            $param = array(
                'page' => 1,
                'limit' => 1000,
                'sort' => 'sort-asc',            
                'active' => 1,            
                'get_latest_arrival' => 1,            
            );
            $result = Api::call('url_products_lists', $param);         
            if (!empty($result['data'])) {
                $result = $result['data'];
                Cache::set($key, $result);
            }
        }        
        return $result;
    }   
    
    public static function featured()
    {
        $param = array();
        $key = PRODUCT_FEATURED . WebModule::getConfig('website_id');
        if (!($result = Cache::get($key))) {
            $param = array(
                'page' => 1,
                'limit' => 1000,
                'sort' => 'sort-asc',            
                'active' => 1,            
                'get_featured' => 1,            
            );
            $result = Api::call('url_products_lists', $param);         
            if (!empty($result['data'])) {
                $result = $result['data'];
                Cache::set($key, $result);
            }
        }        
        return $result;
    }
    
    public static function topSeller()
    {
        $param = array();
        $key = PRODUCT_TOP_SELLER . WebModule::getConfig('website_id');
        if (!($result = Cache::get($key))) {
            $param = array(
                'page' => 1,
                'limit' => 1000,
                'sort' => 'sort-asc',            
                'active' => 1,            
                'get_top_seller' => 1,            
            );
            $result = Api::call('url_products_lists', $param);         
            if (!empty($result['data'])) {
                $result = $result['data'];
                Cache::set($key, $result);
            }
        }        
        return $result;
    }
    
    public static function related($productId, $categoryId)
    {
        $param = array();
        $key = PRODUCT_RELATED . WebModule::getConfig('website_id') . '_' . $productId;
        if (!($result = Cache::get($key))) {
            $param = array(
                'page' => 1,
                'limit' => 8,                  
                'active' => 1,
                'category_id' => $categoryId,
                'not_in_product_id' => $productId,
            );
            $result = Api::call('url_products_all', $param); 
            if (!empty($result)) {          
                Cache::set($key, $result);
            }
        }
        return $result;
    }
    
    public static function homepage()
    {
        $param = array();
        $key = PRODUCT_HOMEPAGE . WebModule::getConfig('website_id');
        if (!($result = Cache::get($key))) {
            $param = array();
            $result = Api::call('url_products_homepage', $param);         
            if (!empty($result)) {               
                Cache::set($key, $result);                
            }
        }        
        return $result;
    } 
    
}
