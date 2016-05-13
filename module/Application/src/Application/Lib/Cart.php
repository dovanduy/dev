<?php

namespace Application\Lib;

use Zend\Session\Container;
use Application\Model\Images;

/**
 * Cart
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Cart {
    
    protected static $instance;
    protected static $sessionName = 'cart';
        
    /**
     * Return the Zend\Log\Logger instance
     *     
     * @author 	thailh
     * @return	Zend\Log\Logger
     */    
	public static function instance()
	{  
        static::$instance or static::_init();
        return static::$instance;     
	}
    
    /**
     * Initialize the class
     *  
     * @author 	thailh 
     * @return	void
     */      
	public static function _init()
	{      
        static::$instance = new Container(static::$sessionName);        
    }    
   
    public static function reset() 
    {
        if (static::instance()->offsetExists(static::$sessionName)) {
            return static::instance()->offsetUnset(static::$sessionName);
        }
        return true;
    }
    
	public static function get($moneyFormat = false) 
    {
        if (static::instance()->offsetExists(static::$sessionName)) {
            $items = static::instance()->offsetGet(static::$sessionName); 
            if ($moneyFormat == true) {
                foreach ($items as &$item) {
                    $item['total_money'] = money_format($item['total_money']);
                } 
                unset($item);
            }
            return $items;
        }
        return false;
    }
    
    public static function addProduct($id, $quantity = 1, $sizeId = 0) 
    { 
        $keyId = $id . '_' . $sizeId;
        $quantity = db_int($quantity);
        $items = static::get();
        if (isset($items[$keyId])) {
            $quantity = $items[$keyId]['quantity'] + $quantity;
            $items[$keyId]['quantity'] = $quantity;
            $items[$keyId]['total_money'] = $quantity * db_float($items[$keyId]['price']);
        } else {
            $data = Api::call(
                'url_products_detail', 
                array(
                    '_id' => $id, 
                )
            );
            if (empty($data)) {
                return false;
            }
            $price = $data['price'];
            $name = $data['name'];
            if (!empty($sizeId)) {
                foreach ($data['sizes'] as $size) {
                    if ($size['size_id'] == $sizeId && !empty($size['price'])) {
                        $price = $size['price'];
                        $name = $name . ' (' . $size['name'] .')';
                    }
                }
            }
            $data['url_image'] = '';
            if (!empty($data['image_id'])) {
                $data['url_image'] = Images::getUrl($data['image_id'], 'products', true);
            }
            $items[$keyId] = array(
                '_id' => $data['_id'],
                'product_id' => $data['product_id'],
                'size_id' => $sizeId,
                'name' => $name,                
                'quantity' => $quantity,
                'price' => db_float($price),
                'original_price' => db_float($data['original_price']),                
                'total_money' => $quantity * db_float($price),
                'url_image' => $data['url_image'],
            );
        }            
        static::instance()->offsetSet(static::$sessionName, $items);
        return $items;
    }
    
    public static function removeProduct($keyId) 
    { 
        $items = static::get();
        if (isset($items[$keyId])) {
            unset($items[$keyId]);
            static::instance()->offsetSet(static::$sessionName, $items);
            return $items;
        } 
        return false;
    }
    
    public static function update($param) 
    { 
        $items = static::get();
        if (empty($param['quantity'])) {
            return $items;
        }
        if (empty($param['price'])) {
            $param['price'] = array();
            foreach ($items as $keyId => $item) {
                $param['price'][$keyId] = $item['price'];
            }
        }
        
        foreach ($param['quantity'] as $qKeyId => $quantity) {
            $quantity = db_int($quantity);
            $findKeyId = null;
            foreach ($items as $keyId => $item) {
                if ($keyId == $qKeyId) {
                    $findKeyId = $qKeyId; 
                    break;
                }                               
            }
            if (!empty($findKeyId) && isset($param['price'][$findKeyId])) {
                $price = $param['price'][$findKeyId];
                $items[$findKeyId]['quantity'] = $quantity;
                $items[$findKeyId]['price'] = db_float($price);
                $items[$findKeyId]['total_money'] = $quantity * db_float($price);
            }
        }
        static::instance()->offsetSet(static::$sessionName, $items);
        return $items;
    }

}

