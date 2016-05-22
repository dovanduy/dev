<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
     'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=dev2;host=localhost',
        'username' => 'root',
        'password' => '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'db_oauth2' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=dev2;host=localhost',
        'username' => 'root',
        'password' => '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    /*
    'db_test' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=test;host=103.20.148.47',
        'username' => '123yougo',
        'password' => '123yougo@123',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'db_cores' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=yg.cores;host=103.20.148.47',
        'username' => '123yougo',
        'password' => '123yougo@123',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'db_histories' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=yg.histories;host=103.20.148.47',
        'username' => '123yougo',
        'password' => '123yougo@123',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'db_hotels' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=yg.hotels;host=103.20.148.47',
        'username' => '123yougo',
        'password' => '123yougo@123',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'db_places' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=yg.places;host=103.20.148.47',
        'username' => '123yougo',
        'password' => '123yougo@123',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'db_images' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=yg.images;host=103.20.148.47',
        'username' => '123yougo',
        'password' => '123yougo@123',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'db_searchs' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=yg.searchs;host=103.20.148.47',
        'username' => '123yougo',
        'password' => '123yougo@123',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    */
    'service_manager' => array(
        'factories' => array(
            'db' => 'Zend\Db\Adapter\AdapterServiceFactory',
            /*
            'db_test' => function($sm) {
                 $config = $sm->get('Config');
                 return new Zend\Db\Adapter\Adapter($config['db_test']);
            },
            'db_cores' => function($sm) {
                 $config = $sm->get('Config');
                 return new Zend\Db\Adapter\Adapter($config['db_cores']);
            },
            'db_histories' => function($sm) {
                 $config = $sm->get('Config');
                 return new Zend\Db\Adapter\Adapter($config['db_histories']);
            },
            'db_hotels' => function($sm) {
                 $config = $sm->get('Config');
                 return new Zend\Db\Adapter\Adapter($config['db_hotels']);
            },
            'db_places' => function($sm) {
                 $config = $sm->get('Config');
                 return new Zend\Db\Adapter\Adapter($config['db_places']);
            },
            'db_images' => function($sm) {
                 $config = $sm->get('Config');
                 return new Zend\Db\Adapter\Adapter($config['db_images']);
            },
            'db_searchs' => function($sm) {
                 $config = $sm->get('Config');
                 return new Zend\Db\Adapter\Adapter($config['db_searchs']);
            },  */          
        ),
    ),
        
    'module_layouts' => array(
       'Application' => 'layout/layout.phtml',
       'Admin' => 'admin/layout.phtml',
       'Web' => 'web/layout.phtml',
   ),
);
