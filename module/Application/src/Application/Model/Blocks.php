<?php

namespace Application\Model;

use Application\Lib\Auth;
use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

class Blocks
{

    public static function removeCache()
    {
		$key = BLOCKS_ALL;    
		$auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = $key . $AppUI->website_id;
        }
        Cache::remove($key);
    }
    
    public static function getAll($forDropdownList = true)
    {
        $param = array();
        $key = BLOCKS_ALL;    
		$auth = new Auth();        
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $key = $key . $AppUI->website_id;
        }
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_blocks_all', $param);
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }       
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                '_id', 
                'name'
            );
        }
        return $data;
    }
    
}
