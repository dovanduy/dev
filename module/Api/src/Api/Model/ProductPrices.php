<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductPrices extends AbstractModel {
    
    protected static $properties = array(
        'product_id',
        'size_id',
        'color_id',
        'price',      
        'created',
        'updated',
        'active',
        'website_id',
    );
    
    protected static $primaryKey = array('product_id', 'color_id', 'size_id');
    
    protected static $tableName = 'product_prices';
    
    public function savePrice($param)
    {        
        if (empty($param['product_id'])) {
            self::errorParamInvalid('product_id');
            return false;
        } 
        $productModel = new Products;
        $productDetail = $productModel->find(
            array(            
                'where' => array(
                    'website_id' => $param['website_id'],
                    'product_id' => $param['product_id']
                )
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($productDetail)) {
            self::errorParamInvalid('product_id');
            return false;
        }
        $values = array();
        if (!empty($param['price'])) {            
            $param['price'] = \Zend\Json\Decoder::decode($param['price'], \Zend\Json\Json::TYPE_ARRAY);        
            foreach ($param['price'] as $id => $price) {  
                list($productId, $colorId, $sizeId) = explode('_', $id);             
                $values[] = array(                                  
                    'product_id' => $productId,                       
                    'size_id' => $sizeId,
                    'color_id' => $colorId,
                    'price' => db_float($price),                                                        
                );               
            }
        }        
        if (!empty($values)) {
            $ok = self::batchInsert(
                $values,
                array(                  
                    'price' => new Expression('VALUES(`price`)'),
                ),
                false
            );
            if ($ok) {
                foreach ($values as $value) {
                     if ($value['price'] == $productDetail['price']) {
                        $productModel->update(array(
                           'set' => array(
                               'default_color_id' => $value['color_id'],
                               'default_size_id' => $value['size_id'],
                            ),
                            'where' => array(
                                 'product_id' => $value['product_id'],   
                            )
                        ));
                        break;
                    }
                }
            }
            return $ok;
        }
        return false;
    }
    
}
