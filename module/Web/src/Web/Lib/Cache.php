<?php
namespace Application\Lib;

use Zend\Cache\StorageFactory;
use Application\Module;

/**
 * Functions to write cache
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */

class Cache {
    
    protected static $cache;
    
   /**
     * Return the Zend\Cache\StorageFactory instance
     *     
     * @author 	thailh
     * @return	Zend\Cache\StorageFactory
     */  
    public static function instance()
	{
        // make sure we have an instance
        static::$cache or static::_init();
        return static::$cache;    
    }   
    
    /**
     * Initialize the class
     *  
     * @author 	thailh 
     * @return	void
     */  
    public static function _init()
	{
        static::$cache = StorageFactory::factory(Module::getConfig('cache')); 
    }
    
    /**
     * Get an item.
     *
     * @param  string  $key
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public static function get($key, & $success = null, & $casToken = null) {
        return static::instance()->getItem($key, $success, $casToken);
    }

    /**
     * Get multiple items.
     *
     * @param  array $keys
     * @return array Associative array of keys and values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public static function gets(array $keys) {
        return static::instance()->getItems($keys);
    }
    
    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public static function has($key) {
        return static::instance()->hasItem($key);
    }
    
    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public static function set($key, $value) {
        return static::instance()->setItem($key, $value);
    }
    
    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public static function remove($key) {
        return static::instance()->removeItem($key);        
    }
    
}

