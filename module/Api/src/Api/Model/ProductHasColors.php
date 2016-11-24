<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;
use Application\Lib\Arr;

class ProductHasColors extends AbstractModel {
    
    protected static $properties = array(
        'product_id',        
        'color_id',       
        'image_id', 
        'created',
        'updated',
    );
    
    protected static $primaryKey = array('product_id', 'color_id');
    
    protected static $tableName = 'product_has_colors';
    
    public function getAll($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'color_id', 
                'product_id',
            ))
            ->join(             
                'product_images',                   
                static::$tableName . '.image_id = product_images.image_id',
                array(                   
                    'image_id',
                    'url_image',
                    'is_main',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'product_colors', 
                static::$tableName . '.color_id = product_colors.color_id',
                array()
            )
            ->join(               
                array(
                    'product_color_locales' => 
                    $sql->select()
                        ->from('product_color_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.color_id = product_color_locales.color_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                )  
            ); 
        if (!empty($param['product_id'])) {      
            if (is_array($param['product_id'])) {
                $param['product_id'] = implode(',', $param['product_id']);
            }
            $select->where(static::$tableName . '.product_id IN ('. $param['product_id'] . ')');  
        }
        $data = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );     
        if (!empty($data)) {
            if (empty(Arr::filter($data, 'is_main', 1))) {
                $data[0]['is_main'] = 1;
            }
        }
        return $data;
    }  
    
    public function addUpdate($param)
    {        
        if (!empty($param['color_id']) && !is_array($param['color_id'])) {
            $param['color_id'] = array($param['color_id']);
        }       
        $colors = self::find(
            array(     
                'where' => array(                   
                    'product_id' => $param['product_id']
                )
            )
        );
        $values = array();
        if (!empty($param['color_id'])) {                                 
            foreach ($param['color_id'] as $colorId) {                
                $values[] = array(
                    'product_id' => $param['product_id'],
                    'color_id' => $colorId,
                );
                if (empty($values) || !self::batchInsert($values)) {
                    return false;
                }
            }           
        }           
        if (!empty($colors)) {
            foreach ($colors as $color) {                
                if (!in_array($color['color_id'], $param['color_id'])) {
                    if (!self::delete(
                        array(
                            'where' => array(
                                'product_id' => $param['product_id'],
                                'color_id' => $color['color_id']
                            ),
                        )
                    )) {
                        return false;
                    }
                }
            }
        }
        return true;        
    }
    
    /* for batch */
    public function import($param, $productName)
    { 
        if (empty($param['website_id'])
            || empty($param['colors']) 
            || empty($param['product_id'])) {
            return false;
        }
        $imageModel = new Images;
        $colorModel = new ProductColors;
        $values = array();                     
        foreach ($param['colors'] as $color) { 
            $colorModel->add(
                array(
                    'name' => $color['name'],                    
                    'website_id' => $param['website_id']
                ), 
                $colorId
            );
            if (!empty($colorId)) {
                $imageUrl = '';
                if (!empty($color['url_image'])) {
                    $image = $imageModel->getDetail(array(
                        'src' => 'products',
                        'url_image_source' => $color['url_image']
                    ));
                    if (!empty($image)) {
                        $imageId = $image['image_id'];
                    } else {
                        $imageUrl = Util::uploadImageFromUrl($color['url_image'], 600, 600, $productName); 
                        $imageId = $imageModel->add(array(
                            'src' => 'products',
                            'src_id' => $param['product_id'],
                            'url_image' => $imageUrl,
                            'is_main' => 0,
                            'website_id' => $param['website_id']
                        ));
                    }
                }
                $values[] = array(
                    'color_id' => $colorId,
                    'product_id' => $param['product_id'],
                    'image_id' => !empty($imageId) ? $imageId : 0,
                    'created' => new Expression('UNIX_TIMESTAMP()'),
                    'updated' => new Expression('UNIX_TIMESTAMP()'),
                );
            }
        }
        if (!empty($values) && self::batchInsert(
                $values, 
                array(                    
                    'updated' => new Expression('VALUES(`updated`)'),
                ),
                false
            )
        ) {
            return true;
        }
        return false;       
    }
    
}