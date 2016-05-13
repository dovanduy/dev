<?php

namespace Web\Model;

use Application\Lib\Cache;
use Web\Module as WebModule;
use Web\Lib\Api;

class Banners
{
    public static function removeCache()
    {
        $websiteId = WebModule::getConfig('website_id');
        Cache::remove(BANNER_ALL . $websiteId);   
    }
    
    public static function getAll($page = null)
    {
        $key = BANNER_ALL . WebModule::getConfig('website_id');
        if (!($result = Cache::get($key))) {
            $param = array();
            $result = Api::call('url_banners_all', $param);         
            if (!empty($result)) {
                Cache::set($key, $result);
            }
        }
        return $result;
    } 
    
}
