<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductFeatureds extends AbstractModel {
    
    protected static $properties = array(
        'product_id',        
        'sort',
        'website_id',
        'active',
    );
    
    protected static $primaryKey = 'product_id';
    
    protected static $tableName = 'product_featureds';
    
    public function addUpdate($values)
    {
        if (!self::batchInsert($values, array('active' => '1'))) {
            return false;
        }
        return true;      
    }
    
    public function remove($param)
    {
        if (!self::update(array(
            'set' => array('active' => '0'),
            'where' => array(
                'product_id' => $param['product_id']
            ),
        ))) {
            return false;
        }  
        return true;              
    }
    
    public function updateSort($param) { 
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
    
}
