<?php

namespace Api\Model;

use Application\Lib\Log;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Images extends AbstractModel 
{  
    protected static $properties = array(
		'image_id',
		'_id',
		'src_id',
		'alt_name',
		'url_image',
		'is_main',
        'created',
        'updated',
		'active',
	);
    
    protected static $tableName = 'news_category_images';
    
    public static function getTableName($param)
    {
        if (empty($param['src'])) {
            return false;
        }
        $tableName = '';
        switch ($param['src']) {
            case 'news':
                $tableName = 'news_images';
                break;
            case 'news_categories':
                $tableName = 'news_category_images';
                break;
            case 'website_categories':
                $tableName = 'website_category_images';
                break;
            case 'websites':
                $tableName = 'website_images';
                break;
            case 'product_categories':
                $tableName = 'product_category_images';
                break;
            case 'products':
                $tableName = 'product_images';
                break;            
            case 'admins':
                $tableName = 'admin_images';
                break;
            case 'users':
                $tableName = 'user_images';
                break;
            case 'brands':
                $tableName = 'brand_images';
                break;
            case 'banners':
                $tableName = 'banner_images';
                break;
            case 'menus':
                $tableName = 'menu_images';
                break;
        }
        return $tableName;
    }
    
    public static function add($param)
    {
        if (empty($param['src']) 
            || empty($param['url_image'])) {
            self::errorParamInvalid('src/url_image');
            return false;
        }
        static::$tableName = self::getTableName($param);
        if (empty(static::$tableName)) {
            self::errorParamInvalid('src');
            return false;
        }
        if (empty($param['_id'])) {
            $param['_id'] = mongo_id();
        }
        $values = array(
            '_id' => $param['_id'],
            'src_id' => !empty($param['src_id']) ? $param['src_id'] : 0,
            'url_image' => $param['url_image'],
        );              
        if (isset($param['alt_name'])) {
            $values['alt_name'] = $param['alt_name']; 
        }          
        if (isset($param['is_main'])) {
            $values['is_main'] = $param['is_main']; 
        }
        return self::insert($values);
    }   
    
    public static function multiAdd($param)
    {
        if (empty($param['src']) || empty($param['src_id'])) {
            return false;
        }
        static::$tableName = self::getTableName($param);
        if (empty(static::$tableName)) {
            self::errorParamInvalid('src');
            return false;
        }
        if (!empty($param['remove'])) {
            foreach ($param['remove'] as $id) {
                self::remove(array(
                    'id' => $id,
                    'src' => $param['src']
                ));
            }
        }
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            foreach ($uploadResult as $name => $urlImage) {                
                if (!empty($param['update'][$name])) {
                    self::updateInfo(array(
                        'src' => $param['src'],
                        'id' => $param['update'][$name],
                        'url_image' => $urlImage
                    ));
                } else {
                    self::add(array(
                        'src' => $param['src'],
                        'src_id' => $param['src_id'],
                        'url_image' => $urlImage,
                        'is_main' => 0,
                    ));
                }
            }
        }
        return true;        
    }    
    
    public static function getDetail($param)
    {
        if (empty($param['id']) || empty($param['src'])) {
            return false;
        } 
        static::$tableName = self::getTableName($param);
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
               'image_id',
                '_id',
                'src_id',
                'alt_name',
                'url_image',
                'is_main',
            ))            
            ->where(
                array(
                    static::$tableName . '.image_id' => $param['id']
                )
            );     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
    }  
    
    public static function getAll($param)
    {
        if (empty($param['src_id']) || empty($param['src'])) {
            return false;
        }  
        static::$tableName = self::getTableName($param);
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
               'image_id',
                '_id',
                'src_id',
                'alt_name',
                'url_image',
                'is_main',
            ))            
            ->where(
                array(
                    static::$tableName . '.src_id' => $param['src_id'],
                    static::$tableName . '.active' => 1,
                    static::$tableName . '.is_main' => 0
                )
            );     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );     
    }
    
    public static function remove($param)
    {       
        if (empty($param['id']) || empty($param['src'])) {
            return false;
        }
        if (empty($param['src']) 
            || empty($param['id'])) {
            self::errorParamInvalid('src/id');
            return false;
        }
        static::$tableName = self::getTableName($param);
        return self::delete(array(
            'where' => array('image_id' => $param['id'])
        ));
    } 
    
    public static function updateInfo($param)
    {
        if (empty($param['id']) || empty($param['src'])) {
            return false;
        }
        if (empty($param['src']) 
            || empty($param['id'])) {
            self::errorParamInvalid('src/id');
            return false;
        }
        static::$tableName = self::getTableName($param);           
        $set = array();  
        if (isset($param['url_image'])) {
            $set['url_image'] = $param['url_image']; 
        }
        if (isset($param['src_id'])) {
            $set['src_id'] = $param['src_id']; 
        }
        if (isset($param['alt_name'])) {
            $set['alt_name'] = $param['alt_name']; 
        }
        if (empty(static::$tableName) || empty($set)) {
            self::errorParamInvalid('src/set');
            return false;
        }
        return self::update(
            array(
                'set' => $set,
                'where' => array(
                    'image_id' => $param['id']
                ),
            )
        );
    }
    
}
