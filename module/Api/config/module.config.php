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
            // using the path /api/:controller/:action
            'api' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'images' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route' => '/images[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'images',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\Api' => 'Api\Controller\ApiController',
            'Api\Controller\Validator' => 'Api\Controller\ValidatorController',
            'Api\Controller\Images' => 'Api\Controller\ImagesController',
            'Api\Controller\Locationcountries' => 'Api\Controller\LocationcountriesController',
            'Api\Controller\Locationstates' => 'Api\Controller\LocationstatesController',
            'Api\Controller\Locationcities' => 'Api\Controller\LocationcitiesController',
            'Api\Controller\Users' => 'Api\Controller\UsersController',
            'Api\Controller\Newscategories' => 'Api\Controller\NewscategoriesController',
            'Api\Controller\Productcategories' => 'Api\Controller\ProductcategoriesController',
            'Api\Controller\Websitecategories' => 'Api\Controller\WebsitecategoriesController',
            'Api\Controller\News' => 'Api\Controller\NewsController',
            'Api\Controller\Websites' => 'Api\Controller\WebsitesController',
            'Api\Controller\Admins' => 'Api\Controller\AdminsController',
            'Api\Controller\Inputfields' => 'Api\Controller\InputfieldsController',
            'Api\Controller\Inputoptions' => 'Api\Controller\InputoptionsController',
            'Api\Controller\Products' => 'Api\Controller\ProductsController',
            'Api\Controller\Brands' => 'Api\Controller\BrandsController',
            'Api\Controller\Banners' => 'Api\Controller\BannersController',
            'Api\Controller\Addresses' => 'Api\Controller\AddressesController',
            'Api\Controller\Productorders' => 'Api\Controller\ProductordersController',
            'Api\Controller\Menus' => 'Api\Controller\MenusController',
            'Api\Controller\Pages' => 'Api\Controller\PagesController',
            'Api\Controller\Contacts' => 'Api\Controller\ContactsController',
            'Api\Controller\Productreviews' => 'Api\Controller\ProductreviewsController',
            'Api\Controller\Urlids' => 'Api\Controller\UrlidsController',
            'Api\Controller\Blocks' => 'Api\Controller\BlocksController',
            'Api\Controller\Batch' => 'Api\Controller\BatchController',
            'Api\Controller\Productsizes' => 'Api\Controller\ProductsizesController',
            'Api\Controller\Productcolors' => 'Api\Controller\ProductcolorsController',
            'Api\Controller\Vouchers' => 'Api\Controller\VouchersController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'album/album/index' => __DIR__ . '/../view/album/album/index.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                
            ),
        ),
    ),
);
