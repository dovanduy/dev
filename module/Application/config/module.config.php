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
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),         
            
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
     // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'import-products' => array(
                    'options' => array(
                        // add [ and ] if optional ( ex : [<doname>] )
                        'route' => 'import products [--verbose|-v] <website> <category>', 
                        'defaults' => array(
                            '__NAMESPACE__' => 'Api\Controller',
                            'controller' => 'batch',
                            'action' => 'import'
                        ),
                    ),
                ),
            ),
        ),
    ),
    
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',            
        ),
    ),    
    
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            //'application/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'htmlForm' => __DIR__ . '/../view/partial/htmlForm.phtml',
            'htmlListForm' => __DIR__ . '/../view/partial/htmlListForm.phtml',
            'email/layout' => __DIR__ . '/../view/layout/email.phtml',
            'email/order' => __DIR__ . '/../view/email/order.phtml',
            'email/order_shipping' => __DIR__ . '/../view/email/order_shipping.phtml',
            'email/contact' => __DIR__ . '/../view/email/contact.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    
    'view_helpers' => array(
        'invokables'=> array(
            
        )
    ), 
    
    'log' => array(
        'path' => './data/log',        
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
    
    'general' => array(        
        'site_name' => 'Dev',        
        'locales' => array(
            'vi' => 'Tiếng việt',            
            //'en' => 'English',            
        ),
        'default_is_locale' => '1',  
        'default_locale' => 'vi',  
        'default_country_code' => 'VN',
        'default_state_code' => 'VN-SG',
        'default_limit' => 10,  
    ),
    
    'admins' => array(
        'max_images' => 10
    ),
    
    'product_categories' => array(
        'max_images' => 10
    ),
    
    'news_categories' => array(
        'max_images' => 10
    ),
    
    'website_categories' => array(
        'max_images' => 7
    ),
    
    'websites' => array(
        'max_images' => 10
    ),
    
    'brands' => array(
        'max_images' => 4
    ),
    
    'users' => array(
        'max_images' => 4
    ),
    
    'banners' => array(
        'max_images' => 1
    ),
    
    'menus' => array(
        'max_images' => 1
    ),
    
    'products' => array(
        'max_images' => 10
    ),
    
    'address_name' => array(
        'home' => 'Home address',
        'work' => 'Work address',
    ),
    
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'dirLevel' => 2,
                'cacheDir' => getcwd() . '/data/cache',
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
    
    'email' => array(               
        'from_email' => 'vuongquocbalo@gmail.com',        
        'from_name' => 'no-reply',        
        'smtp' => array(
            'host' => 'smtp.gmail.com',
            'name' => 'gmail.com',
            'port' => 587,
            'username' => 'mail.vuongquocbalo.com@gmail.com',
            'password' => 'balo@2016',
            'timeout' => 2 * 60,
            'ssl' => 'tls',
        )
    ),
    
    // production
    //'facebook_app_id' => '1679604478968266',
    //'facebook_app_secret' => '53bbe4bab920c2dd3bb83855a4e63a94',
    
    // dev
    'facebook_app_id' => '261013080913491',
    'facebook_app_secret' => '0eb33476da975933077a4d4ad094479b',
     
    'admin_user_id' => array(1, 4, 11, 13, 86),
    
    'google_urlshortener' => array(
        'url' => 'https://www.googleapis.com/urlshortener/v1/url',
        'key' => 'AIzaSyDORv1kNObIyAhI9khTjsiX230_dL7xUI4',
        'timeout' => 30,
    ),
    
);
