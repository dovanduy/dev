<?php

namespace Application\Lib;

use Zend\Session\Container;

/**
 * Cart
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Session {
    
    protected static $instance;
    protected static $sessionName = 'MySession';
        
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
    
	public static function get($id = null) 
    {
        if (static::instance()->offsetExists(static::$sessionName)) {
            $items = static::instance()->offsetGet(static::$sessionName); 
            if (!empty($id)) { 
                return isset($items[$id]) ? $items[$id] : false;
            }
            return $items;
        }
        return false;
    }
    
    public static function set($id, $value) 
    {                
        $item = array($id => $value);
        static::instance()->offsetSet(static::$sessionName, $item);
        return $item;
    }
    
    public static function remove($id) 
    { 
        $items = static::get();
        if (isset($items[$id])) {
            unset($items[$id]);
            static::instance()->offsetSet(static::$sessionName, $items);
            return $items;
        } 
        return false;
    }
    
}

