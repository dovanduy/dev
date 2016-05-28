<?php

namespace Api\Model;

use Application\Lib\Arr;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductCategoryHasFields extends AbstractModel {
    
    protected static $properties = array(
        'field_id',        
        'category_id',
        'allow_filter',
        'created',
        'updated',
    );
    
    protected static $primaryKey = array('field_id', 'category_id');
    
    protected static $tableName = 'product_category_has_fields';
    
    public function addUpdate($param)
    {        
        if (!is_array($param['category_id'])) {
            $param['category_id'] = array($param['category_id']);
        }
        $categories = self::find(
            array(     
                'where' => array(
                    'field_id' => $param['field_id']
                )
            )
        );
        $categoryValues = array();                     
        foreach ($param['category_id'] as $categoryId) {                
            $categoryValues[] = array(
                'field_id' => $param['field_id'],
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
                                'field_id' => $param['field_id'],
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
    
    public function addField($param)
    {   
        if (empty($param['field_id']) || empty($param['category_id'])) {
            return false;
        }
        if (empty($param['remove'])) {
            $values = array(
                'field_id' => $param['field_id'],
                'category_id' => $param['category_id'],
            );
            if (!self::batchInsert($values)) {
                return false;
            } 
        } else {
            if (!self::delete(
                array(
                    'where' => array(
                        'field_id' => $param['field_id'],
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
        if (empty($param['category_id'])) {
            return array();
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('input_fields')  
            ->columns(array(                
                'field_id', 
                'type', 
                '_id', 
                'sort'
            ))
            ->join(               
                array(
                    'input_field_locales' => 
                    $sql->select()
                        ->from('input_field_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                'input_fields.field_id = input_field_locales.field_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(  
                array(
                    'product_category_has_fields' => 
                    $sql->select()
                        ->from('product_category_has_fields')
                        ->where("product_category_has_fields.category_id IN (". $param['category_id'] . ')')
                ), 
                'input_fields.field_id = product_category_has_fields.field_id',
                array(
                    'allow_filter',
                    'active' => new Expression("IF(product_category_has_fields.field_id IS NULL, 0, 1)")
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where('input_fields.website_id = ' . self::quote($param['website_id']))
            ->where('input_fields.active = 1')     
            ->order('input_fields.active DESC')
            ->order('input_fields.sort');
        if (!empty($param['type'])) {
            $select->where("input_fields.type = ". self::quote($param['type']));   
        }
        if (isset($param['allow_filter']) && $param['allow_filter'] !== '') {
            $select->where("product_category_has_fields.allow_filter = ". self::quote($param['allow_filter']));   
        }
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
                    Arr::filter($options, 'field_id', $row['field_id'], false, false),
                    'option_id',
                    'name'
                );
            }
            unset($row);
        }
        return $rows;
    }
    
    public function updateAllowFilter($param) {       
        if (!self::update(array(
            'set' => array('allow_filter' => $param['value']),
            'where' => array(
                'category_id' => $param['category_id'],
                'field_id' => $param['field_id']
            ),
        ))) {
            return false;
        }
        return true;
    }
    
}
