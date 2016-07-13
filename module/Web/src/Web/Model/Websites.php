<?php

namespace Web\Model;

use Application\Lib\Cache;

use Web\Lib\Api;
use Web\Module as WebModule;

class Websites
{
    public static function removeCache()
    {
        $websiteId = WebModule::getConfig('website_id');  
        $key = WEBSITE_DETAIL . $websiteId;  
        Cache::remove($key);
    }
    
    public static function getDetail()
    {
        $websiteId = WebModule::getConfig('website_id');     
        $key = WEBSITE_DETAIL . $websiteId;  
        if (!($data = Cache::get($key))) {
            $param = array(
                'website_id' => $websiteId,
                'get_menus' => 1,
                'get_product_categories' => 1,
                'get_banners' => 1,
                'get_brands' => 1,
                'get_blocks' => 1,
            );
            $param['website_id'] = $websiteId;            
            $data = Api::call('url_websites_detail', $param);   
            $data['header_menus'] = \Web\Model\Menus::getSubMenu2($data['menus'], $lastLevel = array(), 0, 0, $type = 'header');
            $data['footer_menus'] = \Web\Model\Menus::getSubMenu2($data['menus'], $lastLevel = array(), 0, 0, $type = 'footer');                    
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }        
        return $data;
    }
    
}
