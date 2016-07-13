<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    
    'log' => array(
        'path' => './data/thoitrang1/log/api',        
    ),
    
	'upload' => array(
        'image' => array(
            'path' => './data/thoitrang1/img/',
            'url' => 'http://img.thoitrang1.vn',
            'size' => array('min' => 1*1024, 'max' => 20*1024*1024), // bytes
            'extension' => array('jpeg', 'jpg', 'gif', 'png'),
            'filename_prefix' => 'thoitrang1_',
        )
    ),
	
	'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'dirLevel' => 2,
                'cacheDir' => getcwd() . '/data/thoitrang1/api',
                'dirPermission' => 0755,
                'filePermission' => 0666,
                'ttl' => 30*24*60*60,
                'namespace' => 'app'
            ),
        ),
        'plugins' => array(
            'exception_handler' => array('throw_exceptions' => false),
            'serializer'
        )
    ),
    
);
