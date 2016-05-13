<?php

namespace Api\Model;

use Application\Lib\Arr;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductCategoryHasBrands extends AbstractModel {
    
    protected static $properties = array(
        'brand_id',        
        'category_id',
    );
    
    protected static $primaryKey = array('brand_id', 'category_id');
    
    protected static $tableName = 'product_category_has_brands';
    
    public function addUpdate($param)
    {        
        if (!is_array($param['category_id'])) {
            $param['category_id'] = array($param['category_id']);
        }
        $categories = self::find(
            array(     
                'where' => array(
                    'brand_id' => $param['brand_id']
                )
            )
        );
        $categoryValues = array();                     
        foreach ($param['category_id'] as $categoryId) {                
            $categoryValues[] = array(
                'brand_id' => $param['brand_id'],
                'category_id' => $categoryId,
            );
            if (!self::batchInsert($categoryValues)) {
                return false;
            }
        }           
        if (!empty($categories)) {
            foreach ($categories as $category) {                
                if (!in_array($category['category_id'], $param['category_id'])) {
                    if (!self::delete(
                        array(
                            'where' => array(
                                'brand_id' => $param['brand_id'],
                                'category_id' => $category['category_id']
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
    
    public function addBrand($param)
    {   
        if (empty($param['brand_id']) || empty($param['category_id'])) {
            return false;
        }
        if (empty($param['remove'])) {
            $values = array(
                'brand_id' => $param['brand_id'],
                'category_id' => $param['category_id'],
            );
            if (!self::batchInsert($values)) {
                return false;
            } 
        } else {
            if (!self::delete(
                array(
                    'where' => array(
                        'brand_id' => $param['brand_id'],
                        'category_id' => $param['category_id']
                    ),
                )
            )) {
                return false;
            }
        }      
        return true;        
    }
    
    public function getAll($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        if (is_array($param['category_id'])) {
            $param['category_id'] = implode(',', $param['category_id']);
        }
        static::$tableName = 'brands';
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('brands')  
            ->columns(array(                
                'brand_id', 
                'type', 
                '_id', 
                'sort'
            ))
            ->join(               
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                'brands.brand_id = brand_locales.brand_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(  
                array(
                    'product_category_has_brands' => 
                    $sql->select()
                        ->from('product_category_has_brands')
                        ->where("product_category_has_brands.category_id IN (". $param['category_id'] . ')')
                ), 
                'brands.brand_id = product_category_has_brands.brand_id',
                array(
                    'active' => new Expression("IF(product_category_has_brands.brand_id IS NULL, 0, 1)")
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where('brands.active = 1')     
            ->order('active DESC')
            ->order('brands.sort');
        $rows = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );      
        if (!empty($rows)) {
            $inputOptions = new InputOptions;
            $options = $inputOptions->getAll(array(
                    'locale' => $param['locale']
                )
            );
            foreach ($rows as &$row) {    
                $row['options']['label'] = $row['name'];
                $row['options']['value_options'] = Arr::keyValue(
                    Arr::filter($options, 'brand_id', $row['brand_id'], false, false),
                    'option_id',
                    'name'
                );
            }
            unset($row);
        }
        return $rows;
    } 
    
}
