<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;
use Application\Lib\Auth;

class Pages
{

    public static function removeCache($_id)
    {
        $key = PAGE_DETAIL . $_id;     
        Cache::remove($key);
    }
    
    public static function getDetail($pageId = null)
    {
        
    }
    
}
