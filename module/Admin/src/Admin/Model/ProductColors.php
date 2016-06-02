<?php

namespace Admin\Model;

use Admin\Lib\Api;
use Application\Lib\Cache;

class ProductColors {    
    
    public static function getAll($productId) {
        $key = PRODUCT_COLOR . $productId;     
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_productcolors_all', array('product_id' => $productId));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

}
