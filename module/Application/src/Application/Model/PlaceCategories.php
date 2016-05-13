<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

class PlaceCategories
{

    public static function getDetail($id, $locale = null)
    {
        $key = PLACE_CATEGORIES_DETAIL . $id;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_place_categories_detail', array('category_id' => $id, 'locale' => $locale));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

    public static function getAll($categoryId = null, $forDropdownList = true)
    {
        $key = CATEGORIES_ALL;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_place_categories_all', array());
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if (!empty($categoryId)) {
            foreach ($data as $k => $d) {
                if ($d['category_id'] == $categoryId) {
                    unset($data[$k]);
                    break;
                }
            }
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'category_id', 
                'name'
            );
        }
        return $data;
    }

}
