<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductHasSizes extends AbstractModel {
    
    protected static $properties = array(
        'product_id',        
        'size_id',       
        'created',
        'updated',
    );
    
    protected static $primaryKey = array('product_id', 'size_id');
    
    protected static $tableName = 'product_has_sizes';
    
    public function getAll($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'size_id', 
                'product_id'
            ))
            ->join(
                'product_sizes', 
                static::$tableName . '.size_id = product_sizes.size_id',
                array('price')
            )
            ->join(               
                array(
                    'product_size_locales' => 
                    $sql->select()
                        ->from('product_size_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.size_id = product_size_locales.size_id',
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
        if (!is_array($param['size_id'])) {
            $param['size_id'] = array($param['size_id']);
        }        
        $sizes = self::find(
            array(     
                'where' => array(                   
                    'product_id' => $param['product_id']
                )
            )
        );
        $sizeValues = array();
        if (!empty($param['size_id'])) {                                 
            foreach ($param['size_id'] as $sizeId) {                
                $sizeValues[] = array(
                    'product_id' => $param['product_id'],
                    'size_id' => $sizeId,
                );
                if (!self::batchInsert($sizeValues)) {
                    return false;
                }
            }           
        }           
        if (!empty($sizes)) {
            foreach ($sizes as $size) {                
                if (!in_array($size['size_id'], $param['size_id'])) {
                    if (!self::delete(
                        array(
                            'where' => array(
                                'product_id' => $param['product_id'],
                                'size_id' => $size['size_id']
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