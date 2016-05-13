<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class BlockProducts extends AbstractModel {
    
    protected static $properties = array(
        'block_id',        
        'product_id',        
        'sort',
        'website_id',
        'active',
    );
    
    protected static $primaryKey = 'product_id';
    
    protected static $tableName = 'block_products';
    
    public function addUpdate($param)
    {
        $productModel = new Products();
        $product = $productModel->find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($product)) {
            self::errorNotExist('product_id');
            return false;
        }
        $values = array(
            'block_id' => $param['block_id'],
            'product_id' => $param['product_id'],
            'website_id' => $product['website_id'],
            'sort' => 
                self::max(
                    array(
                        'table' => 'block_products',
                        'field' => 'sort'
                    ),
                    array(
                        'where' => array(
                            'website_id' => $product['website_id'],
                            'block_id' => $param['block_id'],
                        )
                    )
                ) + 1,
        );        
        if (!self::batchInsert($values, array('active' => '1'))) {
            return false;
        }
        return true;     
    }
    
    public function remove($param)
    {
        $productModel = new Products();
        $product = $productModel->find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($product)) {
            self::errorNotExist('product_id');
            return false;
        }        
        if (!self::update(array(
            'set' => array('active' => '0'),
            'where' => array(
                'block_id' => $param['block_id'],
                'product_id' => $param['product_id']
            ),
        ))) {
            return false;
        }  
        return true;              
    }
    
    public function updateSort($param) { 
        if (empty($param['block_id']) || empty($param['sort'])) {
            self::errorParamInvalid();
            return false;
        }
        $param['sort'] = \Zend\Json\Decoder::decode($param['sort'], \Zend\Json\Json::TYPE_ARRAY);        
        $values = array();
        foreach ($param['sort'] as $id => $sort) {
            $values[] = array(
                'block_id' => $param['block_id'],
                'product_id' => $id,
                'sort' => $sort
            ); 
        }
        return self::batchInsert(
            $values,
            array(
                'sort' => new Expression('VALUES(`sort`)'),
            ),
            false
        );        
    }
    
}
