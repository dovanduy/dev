<?php

namespace Web\Model;

use Application\Lib\Auth;
use Application\Lib\Cache;
use Application\Lib\Arr;
use Web\Module as WebModule;
use Web\Lib\Api;

class Pages
{
    public static function removeCache($pageId)
    {        
		$websiteId = WebModule::getConfig('website_id');
        Cache::remove(PAGES_DETAIL . $pageId);        
        Cache::remove(PAGES_ALL . $websiteId);        
    }
    
    public static function getDetail($pageId = null)
    {
        $key = PAGES_DETAIL . $pageId;
        if (!($result = Cache::get($key))) {
            $param = array('page_id' => $pageId);
            $result = Api::call('url_pages_detail', $param);         
            if (!empty($result)) {
                Cache::set($key, $result);
            }
        } 
        return $result;
    }
	
	public static function getAll()
    {
		$websiteId = WebModule::getConfig('website_id');
        $key = PAGES_ALL . $websiteId;
        if (!($result = Cache::get($key))) {
			$param = array();
            $param['website_id'] = $websiteId;
            $result = Api::call('url_pages_all', $param);         
            if (!empty($result)) {
                Cache::set($key, $result);
            }
        } 
        return $result;
    } 
    
}
