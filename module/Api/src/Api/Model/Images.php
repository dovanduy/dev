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
		'url_image_source',
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
        if (isset($param['url_image_source'])) {
            $values['url_image_source'] = $param['url_image_source']; 
        } 
        return self::insert($values);
    }   
    
    public static function multiAddHasColor($param)
    {
        if (empty($param['src']) || empty($param['src_id'])) {
            return false;
        }
        static::$tableName = self::getTableName($param);
        if (empty(static::$tableName)) {
            self::errorParamInvalid('src');
            return false;
        }
        $productModel = new Products;
        $hasColorModel = new ProductHasColors;
        $currentData = json_decode($param['current'], true);        
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
        }
        $values = array();
        for ($i = 1; $i < \Application\Module::getConfig('products.max_images'); $i++) {
            $imageId = !empty($currentData['image_id']['url_image' . $i]) ? $currentData['image_id']['url_image' . $i] : 0;
            if (!empty($imageId)) { 
                if (!empty($param['remove_url_image' . $i])) { // delete
                    self::delete(array(                        
                        'where' => array(
                            'src_id' => $param['src_id'],
                            'image_id' => $imageId
                        )
                    )); 
                    $hasColorModel->delete(array(
                        'where' => array(
                            'product_id' => $param['src_id'],
                            'image_id' => $imageId
                        )
                    ));
                } else { // update                    
                    $set = array(
                        'is_main' => ($param['is_main'] == 'url_image' . $i) ? 1 : 0
                    );
                    if (!empty($uploadResult['url_image' . $i])) {
                        $set['url_image'] = $uploadResult['url_image' . $i];
                    }                   
                    self::update(array(                        
                        'set' => $set,
                        'where' => array(
                            'src_id' => $param['src_id'],
                            'image_id' => $imageId
                        )
                    ));
                    if ($set['is_main'] == 1) {
                        $productModel->update(array(                            
                            'set' => array('image_id' => $imageId),
                            'where' => array(
                                'product_id' => $param['src_id']
                            )
                        ));
                    }
                    if (!empty($param['color_url_image' . $i])) {
                        $colorId = $param['color_url_image' . $i];
                        $values[] = array(
                            'image_id' => $imageId,
                            'color_id' => $colorId,
                            'product_id' => $param['src_id'],
                            'created' => new Expression('UNIX_TIMESTAMP()'),
                            'updated' => new Expression('UNIX_TIMESTAMP()'),
                        );
                    }
                }
            } elseif (!empty($uploadResult['url_image' . $i])) { // new image 
                $isMain = ($param['is_main'] == 'url_image' . $i) ? 1 : 0;
                $imageId = self::add(array(
                    'src' => $param['src'],
                    'src_id' => $param['src_id'],
                    'url_image' => $uploadResult['url_image' . $i],
                    'is_main' => $isMain
                ));
                if (!empty($imageId) && $isMain == 1) {
                    $productModel->update(array(
                        'set' => array('image_id' => $imageId),
                        'where' => array(
                            'product_id' => $param['src_id']
                        )
                    ));
                }                  
                if (!empty($imageId) && !empty($param['color_url_image' . $i])) {
                    $colorId = $param['color_url_image' . $i];
                    $values[] = array(
                        'image_id' => $imageId,
                        'color_id' => $colorId,
                        'product_id' => $param['src_id'],
                        'created' => new Expression('UNIX_TIMESTAMP()'),
                        'updated' => new Expression('UNIX_TIMESTAMP()'),
                    );
                }  
            }
            Log::info($values);
            if (!empty($values)) {
                self::batchInsert(
                    $values, 
                    array(
                        'product_id' => new Expression('VALUES(`product_id`)'),
                        'color_id' => new Expression('VALUES(`color_id`)'),
                        'image_id' => new Expression('VALUES(`image_id`)'),
                        'updated' => new Expression('UNIX_TIMESTAMP()')
                    ), 
                    false, 
                    'product_has_colors'
                );
            }
        }
        return true;        
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
        if ((empty($param['id']) && empty($param['url_image']) && empty($param['url_image_source'])) 
            || empty($param['src'])) {
            return false;
        }
        static::$tableName = self::getTableName($param);
        $columns = array(                
            'image_id',
             '_id',
             'src_id',
             'alt_name',
             'url_image',
             'is_main',
        );
        if (in_array(static::$tableName, ['product_images'])){
            $columns[] = 'url_image_source';
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns($columns);  
        if (!empty($param['id'])) {           
            $select->where(static::$tableName . '.image_id = '. self::quote($param['id']));  
        }
        if (!empty($param['url_image'])) {            
            $select->where(static::$tableName . '.url_image = '. self::quote($param['url_image']));  
        }
        if (!empty($param['url_image_source'])) {            
            $select->where(static::$tableName . '.url_image_source = '. self::quote($param['url_image_source']));  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
    }  
    
    public static function getAll($param)
    {
        if (empty($param['src'])) {
            return false;
        } 
        static::$tableName = self::getTableName($param);
        $columns = array(                
            'image_id',
             '_id',
             'src_id',
             'alt_name',
             'url_image',
             'is_main',
        );
        if (in_array(static::$tableName, ['product_images'])){
            $columns[] = 'url_image_source';
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns($columns)
            ->order('is_main DESC');    
        $select->where(static::$tableName . '.active = 1');
        if (!empty($param['src_id'])) {
            $select->where(static::$tableName . '.src_id = ' . $param['src_id']);
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );     
    }
    
    public static function getAllHasColor($param)
    {
        if (empty($param['src_id']) || empty($param['src'])) {
            return false;
        }  
        static::$tableName = self::getTableName($param);
        $columns = array(                
            'image_id',
             '_id',
             'src_id',
             'alt_name',
             'image_id',
             'url_image',
             'is_main',
             'url_image_source',
        );        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns($columns)  
            ->join(               
                'product_has_colors',                   
                static::$tableName . '.image_id = product_has_colors.image_id',
                array(
                    'color_id'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT 
            )
            ->where(
                array(
                    static::$tableName . '.src_id' => $param['src_id'],
                    static::$tableName . '.active' => 1
                )
            )
            ->order('is_main DESC');     
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
        if (isset($param['url_image_source'])) {
            $set['url_image_source'] = $param['url_image_source']; 
        }
        if (isset($param['src_id'])) {
            $set['src_id'] = $param['src_id']; 
        }
        if (isset($param['alt_name'])) {
            $set['alt_name'] = $param['alt_name']; 
        }       
        if (isset($param['is_main'])) {
            $set['is_main'] = $param['is_main']; 
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
                )
            )
        );
    }
    
    public static function getForBatch($param)
    {
        $srcs = array(
            'admins',
            'users',
            'banners',            
            'brands',
            'menus',
            
            'websites',
            'website_categories',
            
            'news',
            'news_categories',    
            
            'products',
            'product_categories',                    
        );
        $result = array();
        foreach ($srcs as $src) {
            $images = self::getAll(array(
                'website_id' => $param['website_id'],
                'src' => $src,
            ));
            $result = array_merge($result, $images);
        }
        return $result;
    }
    
}
