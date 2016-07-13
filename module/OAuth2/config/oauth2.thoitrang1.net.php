<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /oauth2/:controller/:action
            'oauth2' => array(
                'type' => 'Hostname',
                'options' => array(
                    'route' => 'oauth2.thoitrang1.net',                    
                ),                
            ),
        ),
    ),    
    
    'log' => array(
        'path' => './data/thoitrang1/log/oauth2',        
    ),
    
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'dirLevel' => 2,
                'cacheDir' => getcwd() . '/data/thoitrang1/cache/oauth2',
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
