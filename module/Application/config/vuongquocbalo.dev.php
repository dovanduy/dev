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
    
    'upload' => array(
        'image' => array(
            'path' => './data/upload/img',
            'url' => 'http://img.vuongquocbalo.dev',
            'size' => array('min' => 1*1024, 'max' => 20*1024*1024), // bytes
            'extension' => array('jpeg', 'jpg', 'gif', 'png'),
            'filename_prefix' => 'balo_',
        )
    ), 
    
    
);
