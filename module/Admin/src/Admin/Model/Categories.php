<?php

namespace Admin\Model;

use Application\Lib\Api;
use Application\Lib\Cache;

class Categories
{

    public static function categories_detail($id, $locale = null)
    {
        $key = CATEGORIES_DETAIL . $id;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_categories_detail', array('category_id' => $id, 'locale' => $locale));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        return $data;
    }

    public static function categoris_list($category_id = null)
    {
        $key = CATEGORIES_LIST;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_categories_all', array());
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if (!empty($category_id)) {
            foreach ($data as $k => $d) {
                if ($d['category_id'] == $category_id) {
                    unset($data[$k]);
                    break;
                }
            }
        }
        return $data;
    }

}
