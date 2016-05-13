<?php

namespace Web\Model;

use Application\Lib\Api;
use Application\Lib\Cache;
use Application\Lib\Arr;

use Web\Module as WebModule;

class Menus
{
    public static function removeCache()
    {
        $websiteId = WebModule::getConfig('website_id');  
        $key = MENU_ALL . $websiteId;  
        Cache::remove($key);
    }
    
    public static function getAll($menuId = null, $forDropdownList = true, $type = 'header')
    {
        $websiteId = WebModule::getConfig('website_id');        
        $key = MENU_ALL . $websiteId;        
        if (!($data = Cache::get($key))) {
            $param = array();
            $param['website_id'] = $websiteId;
            $data = Api::call('url_menus_all', $param);         
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }
        if (!empty($menuId)) {
            foreach ($data as $k => $d) {
                if ($d['menu_id'] == $menuId) {
                    unset($data[$k]);
                    break;
                }
            }
        }
        if ($type == 'header' || $type == 'footer') {
            $data = Arr::filter($data, 'type', $type);
        }
        if ($forDropdownList == true) {
           $data = Arr::keyValue(
                $data, 
                'menu_id', 
                'name'
            );
        }
        return $data;
    }
    
    public static function getForSelect(&$lastLevel, $withoutMenuId = 0, $type = 'header')
    {
        $menus = self::getSubMenu(self::getAll(0, false, $type), $lastLevel);
        $result = array();
        foreach ($menus as $menu) {  
            if ($menu['menu_id'] === $withoutMenuId) {
                continue;
            }
            $result[$menu['menu_id']] = $menu['name'];
            if (!empty($menu['sub'])) {               
                foreach ($menu['sub'] as $sub1) {
                    if ($sub1['menu_id'] === $withoutMenuId) {
                        continue;
                    }
                    $result[$sub1['menu_id']] = $sub1['name'];
                    if (!empty($sub1['sub'])) {   
                        foreach ($sub1['sub'] as $sub2) {
                            if ($sub2['menu_id'] === $withoutMenuId) {
                                continue;
                            }
                            $result[$sub2['menu_id']] = $sub2['name'];
                            if (!empty($sub2['sub'])) {   
                                foreach ($sub2['sub'] as $sub3) {
                                    if ($sub3['menu_id'] === $withoutMenuId) {
                                        continue;
                                    }
                                    $result[$sub3['menu_id']] = $sub3['name'];
                                    if (!empty($sub3['sub'])) {   
                                        foreach ($sub3['sub'] as $sub4) {
                                            if ($sub4['menu_id'] === $withoutMenuId) {
                                                continue;
                                            }
                                            $result[$sub4['menu_id']] = $sub4['name'];
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
    
    public static function getSubMenu($menus = array(), &$lastLevel = array(), $parentId = 0, $level = 0, $type = 'header')
    {
        if (empty($menus)) {
            $menus = self::getAll(0, false, $type); 
        }
        $parent = array();
        $rows = array();
        if (!empty($menus)) {
            foreach ($menus as $menu) {  
                if ($menu['menu_id'] == $parentId) {
                    $parent = $menu;
                }
                if ($menu['parent_id'] == $parentId) {      
                    $menu['level'] = $level;
                    $prefixName = '';
                    for ($i = 0; $i < $level; $i++) {                   
                        if ($i < $level - 1) {
                            $prefixName .= '&#160;&#160;&#160;&#160;&#160;&#160;&#160;';
                        } else {
                            $prefixName .= '|----';
                        }
                    }
                    $menu['name'] = $prefixName . $menu['name'];
                    $rows[] = $menu;           
                }
            }
            if (!empty($rows)) {
                $level++;
                foreach ($rows as $i => $row) {                
                    $rows[$i]['sub'] = self::getSubMenu($menus, $lastLevel, $row['menu_id'], $level);
                    if (empty($rows[$i]['sub'])) {
                        $lastLevel[] = $row['menu_id'];
                    }
                }            
            }
        }
        return $rows;
    }
    
    public static function getSubMenu2($menus = array(), &$lastLevel = array(), $parentId = 0, $level = 0, $type = 'header')
    {
        if (empty($menus)) {
            $menus = self::getAll(0, false, $type); 
        }
        $parent = array();
        $rows = array();
        if (!empty($menus)) {
            foreach ($menus as $menu) {  
                if ($menu['menu_id'] == $parentId) {
                    $parent = $menu;
                }
                if ($menu['parent_id'] == $parentId) {      
                    $menu['level'] = $level;                    
                    $rows[] = $menu;           
                }
            }
            if (!empty($rows)) {
                $level++;
                foreach ($rows as $i => $row) {                
                    $rows[$i]['sub'] = self::getSubMenu2($menus, $lastLevel, $row['menu_id'], $level);
                    if (empty($rows[$i]['sub'])) {
                        $lastLevel[] = $row['menu_id'];
                    }
                }            
            }
        }
        return $rows;
    }
    
}
