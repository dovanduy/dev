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
                    $item['total_money'] = app_money_format($item['total_money']);
                } 
                unset($item);
            }
            return $items;
        }
        return false;
    }
    
    public static function addProduct($id, $quantity = 1) 
    { 
        $quantity = db_int($quantity);
        $items = static::get();
        if (isset($items[$id])) {
            $quantity = $items[$id]['quantity'] + $quantity;
            $items[$id]['quantity'] = $quantity;
            $items[$id]['total_money'] = $quantity * db_float($items[$id]['price']);
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
            $data['url_image'] = '';
            if (!empty($data['image_id'])) {
                $data['url_image'] = Images::getUrl($data['image_id'], 'products', true);
            }
            $items[$id] = array(
                '_id' => $data['_id'],
                'product_id' => $data['product_id'],
                'name' => $data['name'],
                'quantity' => $quantity,
                'price' => db_float($data['price']),
                'original_price' => db_float($data['original_price']),                
                'total_money' => $quantity * db_float($data['price']),
                'url_image' => $data['url_image'],
            );
        }            
        static::instance()->offsetSet(static::$sessionName, $items);
        return $items;
    }
    
    public static function removeProduct($id) 
    { 
        $items = static::get();
        if (isset($items[$id])) {
            unset($items[$id]);
            static::instance()->offsetSet(static::$sessionName, $items);
            return $items;
        } 
        return false;
    }
    
    public static function update($param) 
    { 
        $items = static::get();   
        if (empty($param['quantity']) || empty($param['price'])) {
            return $items;
        }
        foreach ($param['quantity'] as $productId => $quantity) {
            $quantity = db_int($quantity);
            $_id = null;
            foreach ($items as $_id => $item) {
                if ($item['product_id'] == $productId) {
                    $_id = $_id;
                    break;
                }
            }
            if (!empty($_id) && isset($param['price'][$productId])) {
                $price = $param['price'][$productId];
                $items[$_id]['quantity'] = $quantity;
                $items[$_id]['price'] = db_float($price);
                $items[$_id]['total_money'] = $quantity * db_float($price);
            }
        }
        static::instance()->offsetSet(static::$sessionName, $items);
        return $items;
    }

}

