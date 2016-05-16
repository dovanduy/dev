<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Json\Decoder;

class OrderHasProducts extends AbstractModel {
    
    protected static $properties = array(
        'id',
        'order_id',
        'product_id',
        'product_name',
        'size_id',
        'color_id',
        'quantity',
        'price',
        'active',
        'created',
        'updated',
    );
    
    protected static $primaryKey = array('order_id', 'product_id', 'size_id');
    
    protected static $tableName = 'product_order_has_products';
    
    public function add($param)
    {   
        $product = self::find(
            array(     
                'table' => 'products',
                'where' => array(
                    'product_id' => $param['product_id']
                )
            ),
            self::RETURN_TYPE_ONE
        );  
        $detail = self::find(
            array(     
                'where' => array(
                    'order_id' => $param['order_id'],
                    'product_id' => $param['product_id'],
                    'size_id' => $param['size_id'],
                    'active' => 1
                )
            ),
            self::RETURN_TYPE_ONE
        );
        if (!empty($detail)) {
            self::errorDuplicate('product_id');
            return false;
        }
        $values = array(
            'product_id' => $param['product_id'],
            'order_id' => $param['order_id'],
            'size_id' => $param['size_id'],
            'color_id' => $param['color_id'],
            'quantity' => $param['quantity'],
            'price' => $product['price'],
        );
        if (self::batchInsert(
                $values, 
                array(
                    'quantity' => new Expression('VALUES(`quantity`)'),
                    'price' => new Expression('VALUES(`price`)'),
                    'active' => 1,
                ),
                false
            )
        ) {
            return true;
        }
        return false;        
    }
    
    public function addUpdate($param)
    {  
        $products = self::find(
                        array(     
                            'where' => array(
                                'order_id' => $param['order_id']
                            )
                        )
                    );         
        $values = array();                     
        foreach ($param['product'] as $productId => $value) { 
            $values[] = array(
                'product_id' => $productId,
                'order_id' => $param['order_id'],
                'size_id' => $param['size_id'],
                'color_id' => $param['color_id'],
                'quantity' => $value['quantity'],
                'price' => $value['price'],
            );      
        }
        
        if (self::batchInsert(
                $values, 
                array(
                    'quantity' => new Expression('VALUES(`quantity`)'),
                    'price' => new Expression('VALUES(`price`)'),
                ),
                false
            )
        ) {           
            if (!empty($products)) {
                foreach ($products as $product) {               
                    if (!in_array($product['product_id'], array_keys($param['product']))) {
                        if (!self::delete(
                            array(
                                'where' => array(
                                    'product_id' => $product['product_id'],
                                    'order_id' => $param['order_id'],
                                    'size_id' => $param['size_id'],
                                    'color_id' => $param['color_id'],
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
                'id', 
                'order_id', 
                'product_id', 
                'product_name', 
                'size_id', 
                'color_id', 
                'quantity', 
                'price',
                'active',
                'total_money' => new Expression("product_order_has_products.quantity * product_order_has_products.price")
            ))  
            ->join(
                'products', 
                static::$tableName . '.product_id = products.product_id',
                array()
            )
            ->join(
                'product_locales', 
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )           
            ->join(
                'product_images', 
                'products.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where('product_locales.locale = '. static::quote($param['locale']));
        if (isset($param['active'])) { 
            $select->where(static::$tableName . '.active = ' . static::quote($param['active']));
        }
        if (!empty($param['order_id'])) {      
            if (is_array($param['order_id'])) {
                $param['order_id'] = implode(',', $param['order_id']);
            }
            $select->where(static::$tableName . '.order_id IN ('. $param['order_id'] . ')');  
        }
        if (!empty($param['product_id'])) {      
            if (is_array($param['product_id'])) {
                $param['product_id'] = implode(',', $param['product_id']);
            }
            $select->where(static::$tableName . '.product_id IN ('. $param['product_id'] . ')');  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
    } 
    
    public function saveDetail($param)
    {        
        if (empty($param['order_id'])) {
            self::errorParamInvalid();
            return false;
        }    
        $values = array();
        if (isset($param['products'])) {
            $products = \Zend\Json\Decoder::decode($param['products'], \Zend\Json\Json::TYPE_ARRAY);            
            foreach ($products as $product) {
                $values[] = array(
                    'order_id' => $param['order_id'],               
                    'product_id' => $product['product_id'],
                    'product_name' => !empty($product['custom_name']) ? $product['custom_name'] : $product['name'],
                    'size_id' => $product['size_id'],
                    'color_id' => $product['color_id'],
                    'quantity' => $product['quantity'],                
                    'price' => $product['price'],                
                );
            }       
        } elseif (isset($param['quantity']) && isset($param['price'])) { 
            $param['quantity'] = \Zend\Json\Decoder::decode($param['quantity'], \Zend\Json\Json::TYPE_ARRAY);        
            $param['price'] = \Zend\Json\Decoder::decode($param['price'], \Zend\Json\Json::TYPE_ARRAY);        
            foreach ($param['quantity'] as $id => $quantity) {     
                $find = self::find(
                    array(     
                        'where' => array(
                            'id' => $id
                        )
                    ),
                    self::RETURN_TYPE_ONE
                );               
                if (!empty($find)) {
                    $value = array(
                        'order_id' => $param['order_id'],               
                        'product_id' => $find['product_id'],                       
                        'size_id' => $find['size_id'],
                        'color_id' => $find['color_id'],
                        'quantity' => $quantity,                                                        
                    );
                    if (isset($param['price'][$id])) {
                        $value['price'] = Util::toPrice($param['price'][$id]);
                    }
                    $values[] = $value;
                }
            } 
        }
        if (!empty($values)) {
            return self::batchInsert(
                $values,
                array(
                    'quantity' => new Expression('VALUES(`quantity`)'),
                    'price' => new Expression('VALUES(`price`)'),
                ),
                false
            );
        }
        return false;
    }   
    
    public function onoff($param)
    {
        if (!self::update(array(
            'set' => array('active' => $param['active']),
            'where' => array(
                'product_id' => $param['product_id'],
                'order_id' => $param['order_id'],
            ),
        ))) {
            return false;
        }  
        return true;
    }
}
