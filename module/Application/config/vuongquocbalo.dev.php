<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'api' => array(
        'base_uri' => 'http://api.vuongquocbalo.dev/',
        'oauth2_base_uri' => 'http://oauth2.vuongquocbalo.dev/'
	),	
    
    'log' => array(
        'path' => './data/vuongquocbalo/log/web',        
    ),   
    
    'upload' => array(
        'image' => array(
            'path' => './data/vuongquocbalo/img/',
            'url' => 'http://img.vuongquocbalo.com',
            'size' => array('min' => 1*1024, 'max' => 20*1024*1024), // bytes
            'extension' => array('jpeg', 'jpg', 'gif', 'png'),
            'filename_prefix' => 'vuongquocbalo_',
        )
    ),
    
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'dirLevel' => 2,
                'cacheDir' => getcwd() . '/data/vuongquocbalo/cache/web',
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
