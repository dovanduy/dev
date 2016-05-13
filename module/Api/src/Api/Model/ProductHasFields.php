<?php

namespace Api\Model;

use Application\Lib\Arr;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductHasFields extends AbstractModel {
    
    protected static $properties = array(
        'product_id',
        'field_id',
        'value_id',
        'value',
    );
    
    protected static $primaryKey = array('field_id', 'product_id');
    
    protected static $tableName = 'product_has_fields';
    
    public function addUpdate($param)
    { 
        $inputFields = new InputFields;
        $field = Arr::keyValue(
            $inputFields->getAll(array(
                'field_id' => array_keys($param['field'])
            )),
            'field_id', // field_id:key
            'type' // type:value
        );
        $attributes = self::find(
            array(     
                'where' => array(
                    'product_id' => $param['product_id']
                )
            )
        ); 
        $values = array();                     
        foreach ($param['field'] as $fieldId => $value) {   
            if (isset($field[$fieldId]) 
                && in_array($field[$fieldId], array('select', 'checkbox', 'radio'))) {
                $values[] = array(
                    'field_id' => $fieldId,
                    'product_id' => $param['product_id'],
                    'value_id' => '[' . (is_array($value) ? implode('],[', $value) : $value) . ']',
                    'value' => '',
                );
            } else {
                $values[] = array(
                    'field_id' => $fieldId,
                    'product_id' => $param['product_id'],
                    'value_id' => 0,
                    'value' => $value,
                );
            }           
        }
        if (self::batchInsert(
                $values, 
                array(
                    'value_id' => new Expression('VALUES(`value_id`)'),
                    'value' => new Expression('VALUES(`value`)'),
                ),
                false
            )
        ) {           
            if (!empty($attributes)) {
                foreach ($attributes as $attribute) {                
                    if (!in_array($attribute['field_id'], array_keys($param['field']))) {
                        if (!self::delete(
                            array(
                                'where' => array(
                                    'field_id' => $attribute['field_id'],
                                    'product_id' => $param['product_id'],
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
        return false;        
    }
    
    public function getAll($param) {    
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'product_id', 
                'field_id', 
                'value_id', 
                'value'
            )) 
            ->join(               
                array(
                    'input_field_locales' => 
                    $sql->select()
                        ->from('input_field_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.field_id = input_field_locales.field_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.active = 1');
        if (!empty($param['product_id'])) {      
            if (is_array($param['product_id'])) {
                $param['product_id'] = implode(',', $param['product_id']);
            }
            $select->where(static::$tableName . '.product_id IN ('. $param['product_id'] . ')');  
        }
        if (!empty($param['field_id'])) {      
            if (is_array($param['field_id'])) {
                $param['field_id'] = implode(',', $param['field_id']);
            }
            $select->where(static::$tableName . '.field_id IN ('. $param['field_id'] . ')');  
        }
        $data = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
        if (!empty($data)) {
            foreach ($data as &$row) {
                if (!empty($row['value_id'])) {
                    $row['value_id'] = str_replace(array('[', ']'), '', $row['value_id']);
                }
            }
            unset($row);
        }
        return $data;
    } 
    
}
