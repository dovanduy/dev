<?php

namespace Web\Model;

use Application\Lib\Auth;
use Application\Lib\Cache;
use Application\Lib\Arr;
use Web\Module as WebModule;
use Web\Lib\Api;

class ProductCategories
{
    public static function removeCache()
    {
        $websiteId = WebModule::getConfig('website_id');
        $key = PRODUCT_CATEGORIES_ALL . $websiteId;        
        Cache::remove($key);
    }
    
    public static function getAll($categoryId = null, $forDropdownList = true, $featured = null)
    {
        $websiteId = WebModule::getConfig('website_id');
        $param = array();
        $key = PRODUCT_CATEGORIES_ALL . $websiteId;   
        $param['website_id'] = $websiteId;
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_productcategories_all', $param);         
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if ($featured == true) {
            $data = Arr::filter($data, 'featured', 1);
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
    
    public static function getSubCategories($categories = array(), &$lastLevel = array(), $parentId = 0, $level = 0, $hasPrefixName = true)
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
                if ($hasPrefixName) {                    
                    $prefixName = '';
                    for ($i = 0; $i < $level; $i++) {                   
                        if ($i < $level - 1) {
                            $prefixName .= '&#160;&#160;&#160;&#160;&#160;&#160;&#160;';
                        } else {
                            $prefixName .= '|----';
                        }
                    }
                    $category['name'] = $prefixName . $category['name'];
                }
                $rows[] = $category;           
            }
        }
        if (!empty($rows)) {
            $level++;
            foreach ($rows as $i => $row) {                
                $rows[$i]['sub'] = self::getSubCategories($categories, $lastLevel, $row['category_id'], $level, $hasPrefixName);
                if (empty($rows[$i]['sub'])) {
                    $lastLevel[] = $row['category_id'];
                }
            }            
        }
        return $rows;
    }
    
    public static function find($categories, $categoryId)
    {         
        foreach ($categories as $category) {
            if ($category['category_id'] == $categoryId) {
                return $category;
            }
         }
         return null;  
    }
    
    public static function findAll($categories, $categoryId)
    {
        $result = array();
        if (empty($categories)) {
            $categories = self::getAll(0, false); 
        }
        $find = self::find($categories, $categoryId); 
        if (!empty($find)) {
            $result[] = $find;
            while (!empty($find['parent_id'])) {
                $find = self::find($categories, $find['parent_id']); 
                if (!empty($find)) {
                    $result[] = $find;
                }
            }
        }
        return array_reverse($result);    
    }   

	public static function getAllBrand($categoryId)
    {
        $websiteId = WebModule::getConfig('website_id');
        $param = array();
        $key = PRODUCT_CATEGORIES_ALLBRAND . $websiteId . md5($categoryId);   
        $param['website_id'] = $websiteId;
        $param['category_id'] = $categoryId;       
        if (!($data = Cache::get($key))) {
            $data = Api::call('url_productcategories_allbrand', $param);        
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }        
        return $data;
    }	
    
    public static function getFilter($categoryId = 0, $brandId = 0)
    {
        $websiteId = WebModule::getConfig('website_id');
        $param = array();        
		if (!empty($categoryId) && !empty($brandId)) {
			$key = PRODUCT_CATEGORIES_FILTER . '_category_brand_' . $websiteId . '_' . md5($categoryId . $brandId);
            $param['category_id'] = $categoryId;       
            $param['brand_id'] = $brandId;       
            if (!($data = Cache::get($key))) {
                $data = Api::call('url_productcategories_filter', $param);        
                if (!empty($data)) {
                    Cache::set($key, $data);
                }
            } 
		} elseif (!empty($categoryId)) {
            $key = PRODUCT_CATEGORIES_FILTER . '_category_' . $websiteId . md5($categoryId);
            $param['category_id'] = $categoryId;       
            if (!($data = Cache::get($key))) {
                $data = Api::call('url_productcategories_filter', $param);        
                if (!empty($data)) {
                    Cache::set($key, $data);
                }
            }      
        } elseif (!empty($brandId)) {
            $key = PRODUCT_CATEGORIES_FILTER . '_brand_' . $websiteId . md5($brandId);
            $param['brand_id'] = $brandId;       
            if (!($data = Cache::get($key))) {
                $data = Api::call('url_productcategories_filter', $param);        
                if (!empty($data)) {
                    Cache::set($key, $data);
                }
            }
        }
        return !empty($data) ? $data : array();
    }
    
    public static function findSubCategoryId($category, &$subId)
    {    
        if (empty($category['sub'])) {
            return true;
        }        
        foreach ($category['sub'] as $sub) {
            $subId[] = $sub['category_id'];
            if (!empty($sub['sub'])) {            
                self::findSubCategoryId($sub, $subId);
            }          
         }
         return true;  
    }
    
    public static function hasSubCategory($categoryId, &$lastLevel = array())
    {    
        $subCategories = ProductCategories::getSubCategories(array(), $lastLevel, $categoryId, false);
        return !empty($subCategories) ? true : false;  
    }
    
    public static function getLastCategories($categories = array(), $forDropdownList = true)
    {      
        \Web\Model\ProductCategories::getSubCategories($categories, $lastLevel, 0, 0, false);
        $data = array();
        foreach ($categories as $category) {
            if (in_array($category['category_id'], $lastLevel)) {
               $data[] = $category;
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
