<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

class WebsiteCategories
{

    public static function removeCache()
    {
        $key = WEBSITE_CATEGORIES_ALL;
        Cache::remove($key);
    }
    
    public static function getAll($categoryId = null, $forDropdownList = true)
    {
        $key = WEBSITE_CATEGORIES_ALL;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_website_categories_all', array());
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
    
    public static function getForSelect(&$lastLevel, $withoutCategoryId = 0)
    {
        $categories = self::getSubCategories(self::getAll(0, false), $lastLevel);       
        $result = array();
        foreach ($categories as $category) {  
            if ($category['category_id'] === $withoutCategoryId) {
                continue;
            }
            $result[$category['category_id']] = $category['name'];
            if (!empty($category['sub'])) {               
                foreach ($category['sub'] as $sub1) {
                    if ($sub1['category_id'] === $withoutCategoryId) {
                        continue;
                    }
                    $result[$sub1['category_id']] = $sub1['name'];
                    if (!empty($sub1['sub'])) {   
                        foreach ($sub1['sub'] as $sub2) {
                            if ($sub2['category_id'] === $withoutCategoryId) {
                                continue;
                            }
                            $result[$sub2['category_id']] = $sub2['name'];
                            if (!empty($sub2['sub'])) {   
                                foreach ($sub2['sub'] as $sub3) {
                                    if ($sub3['category_id'] === $withoutCategoryId) {
                                        continue;
                                    }
                                    $result[$sub3['category_id']] = $sub3['name'];
                                    if (!empty($sub3['sub'])) {   
                                        foreach ($sub3['sub'] as $sub4) {
                                            if ($sub4['category_id'] === $withoutCategoryId) {
                                                continue;
                                            }
                                            $result[$sub4['category_id']] = $sub4['name'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
    
    public static function getSubCategories($categories = array(), &$lastLevel = array(), $parentId = 0, $level = 0)
    {
        if (empty($categories)) {
            $categories = self::getAll(0, false); 
        }
        $parent = array();
        $rows = array();
        foreach ($categories as $category) {  
            if ($category['category_id'] == $parentId) {
                $parent = $category;
            }
            if ($category['parent_id'] == $parentId) {      
                $category['level'] = $level;
                $prefixName = '';
                for($i = 0; $i < $level; $i++) {
                    if ($i < $level - 1) {
                        $prefixName .= '&#160;&#160;&#160;&#160;&#160;&#160;&#160;';
                    } else {
                        $prefixName .= '|----';
                    }
                }
                $category['name'] = $prefixName . $category['name'];
                $rows[] = $category;           
            }
        }
        if (!empty($rows)) {
            $level++;
            foreach ($rows as $i => $row) {
                $rows[$i]['sub'] = self::getSubCategories($categories, $lastLevel, $row['category_id'], $level);
                if (empty($rows[$i]['sub'])) {
                    $lastLevel[] = $row['category_id'];
                }
            }            
        }
        return $rows;
    }
    
}
