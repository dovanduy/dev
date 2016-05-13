<?php
namespace Application\Lib;

use Zend\Cache\StorageFactory;
use Application\Module;
use Web\Module as WebModule;
use Admin\Module as AdminModule;

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
        if (!empty(WebModule::getConfig('cache'))) {
            $cacheConf = WebModule::getConfig('cache');
        } elseif (!empty(AdminModule::getConfig('cache'))) {
            $cacheConf = AdminModule::getConfig('cache');
        } else {            
            $cacheConf = Module::getConfig('cache');
        }
        static::$cache = StorageFactory::factory($cacheConf);       
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

    /**
     * Flush the whole storage
     *
     * @throws Exception\RuntimeException
     * @return bool
     */
    public static function flush()
    {      
        return static::instance()->flush();     
    }
    
    /**
     * Set tags to an item by given key.
     * An empty array will remove all tags.
     *
     * @param string   $key
     * @param string[] $tags
     * @return bool
     */
    public static function setTags($key, array $tags)
    {
        return static::instance()->setTags($key, $tags);
    }
    
    /**
     * Remove items matching given tags.
     *
     * If $disjunction only one of the given tags must match
     * else all given tags must match.
     *
     * @param string[] $tags
     * @param  bool  $disjunction
     * @return bool
     */
    public static function clearByTags(array $tags, $disjunction = false)
    {
        return static::instance()->clearByTags($tags, $disjunction); 
    }
    
    /**
     * Remove items by given namespace
     *
     * @param string $namespace
     * @throws Exception\RuntimeException
     * @return bool
     */
    public static function clearByNamespace($namespace)
    {
        return static::instance()->clearByNamespace($namespace);     
    }
    
    /**
     * Remove items matching given prefix
     *
     * @param string $prefix
     * @throws Exception\RuntimeException
     * @return bool
     */
    public static function clearByPrefix($prefix)
    {
        return static::instance()->clearByPrefix($prefix);
    }
    
}

