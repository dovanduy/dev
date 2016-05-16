<?php

namespace Web\Model;

use Web\Lib\Api;
use Application\Lib\Cache;

class UrlIds {    
    
    public static function getDetail($url, &$categoryId, &$brandId, &$productId, &$optionId, &$pageId = 0) {
        $key = URLIDS_DETAIL . md5($url);     
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_urlids_detail', array('url' => $url));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if (!empty($data['category_id'])) {
            $categoryId = $data['category_id'];
        } elseif (!empty($data['brand_id'])) {
            $brandId = $data['brand_id'];
        } elseif (!empty($data['product_id'])) {
            $productId = $data['product_id'];
        } elseif (!empty($data['option_id'])) {
            $optionId = $data['option_id'];
        } elseif (!empty($data['page_id'])) {
            $pageId = $data['page_id'];
        }
        return $data;
    }

}
