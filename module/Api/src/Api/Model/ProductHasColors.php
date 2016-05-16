<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductHasColors extends AbstractModel {
    
    protected static $properties = array(
        'product_id',        
        'color_id',       
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
                'product_id'
            ))
            ->join(
                'product_colors', 
                static::$tableName . '.color_id = product_colors.color_id',
                array('price')
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
            $select->where(static::$tableName . '.product_id = '. self::quote($param['product_id']));  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }  
    
    public function addUpdate($param)
    {        
        if (!is_array($param['color_id'])) {
            $param['color_id'] = array($param['color_id']);
        }        
        $sizes = self::find(
            array(     
                'where' => array(                   
                    'product_id' => $param['product_id']
                )
            )
        );
        $sizeValues = array();
        if (!empty($param['color_id'])) {                                 
            foreach ($param['color_id'] as $sizeId) {                
                $sizeValues[] = array(
                    'product_id' => $param['product_id'],
                    'color_id' => $sizeId,
                );
                if (!self::batchInsert($sizeValues)) {
                    return false;
                }
            }           
        }           
        if (!empty($sizes)) {
            foreach ($sizes as $size) {                
                if (!in_array($size['color_id'], $param['color_id'])) {
                    if (!self::delete(
                        array(
                            'where' => array(
                                'product_id' => $param['product_id'],
                                'color_id' => $size['color_id']
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
    
}