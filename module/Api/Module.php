<?php

namespace Api;

use Api\Model;
use Zend\Mvc\MvcEvent;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $translator = $e->getApplication()->getServiceManager()->get('translator');         
        $translator->setLocale('vi_VN');        
    }

    public function getConfig($name = '', $default = null) {
        $config = include __DIR__ . '/config/module.config.php';
        
        if (isset($_SERVER['SERVER_NAME']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SERVER['SERVER_NAME'] . '.php')) {
            $domainConfig = include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SERVER['SERVER_NAME'] . '.php';
            $config = array_replace_recursive($config, $domainConfig);
        }

        if (!empty($name)) {
            return Arr::get($config, $name, $default);
        }
        return $config;
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ),
            ),
        );
    }

    // Add this method:
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Albums' => function ($sm) { // demo
                    return new Model\Albums($sm->get('db'));
                },
                'Images' => function ($sm) {
                    return new Model\Images($sm->get('db'));
                },
                'Users' => function ($sm) {
                    return new Model\Users($sm->get('db'));
                },
                'LocationCountries' => function ($sm) {
                    return new Model\LocationCountries($sm->get('db'));
                },
                'LocationStates' => function ($sm) {
                    return new Model\LocationStates($sm->get('db'));
                },
                'LocationCities' => function ($sm) {
                    return new Model\LocationCities($sm->get('db'));
                },
                'Hotels' => function ($sm) {
                    return new Model\Hotels($sm->get('db'));
                },
                'Places' => function ($sm) {
                    return new Model\Places($sm->get('db'), $sm);
                },
                'NewsCategories' => function ($sm) {
                    return new Model\NewsCategories($sm->get('db'), $sm);
                },
                'News' => function ($sm) {
                    return new Model\News($sm->get('db'));
                },
                'InputOptions' => function ($sm) {
                    return new Model\InputOptions($sm->get('db'));
                },
                'InputFields' => function ($sm) {
                    return new Model\InputFields($sm->get('db'), $sm);
                },
                'WebsiteCategories' => function ($sm) {
                    return new Model\WebsiteCategories($sm->get('db'), $sm);
                },
                'Websites' => function ($sm) {
                    return new Model\Websites($sm->get('db'), $sm);
                },
                'Admins' => function ($sm) {
                    return new Model\Admins($sm->get('db'), $sm);
                }, 
                'ProductCategories' => function ($sm) {
                    return new Model\ProductCategories($sm->get('db'), $sm);
                },
                'Products' => function ($sm) {
                    return new Model\Products($sm->get('db'), $sm);
                },
                'ProductCategoryHasFields' => function ($sm) {
                    return new Model\ProductCategoryHasFields($sm->get('db'), $sm);
                },
                'ProductHasCategories' => function ($sm) {
                    return new Model\ProductHasCategories($sm->get('db'), $sm);
                },
                'Brands' => function ($sm) {
                    return new Model\Brands($sm->get('db'), $sm);
                },
                'Banners' => function ($sm) {
                    return new Model\Banners($sm->get('db'), $sm);
                },
                'Addresses' => function ($sm) {
                    return new Model\Addresses($sm->get('db'), $sm);
                },
                'ProductOrders' => function ($sm) {
                    return new Model\ProductOrders($sm->get('db'), $sm);
                },
                'OrderHasProducts' => function ($sm) {
                    return new Model\OrderHasProducts($sm->get('db'), $sm);
                },
                'Menus' => function ($sm) {
                    return new Model\Menus($sm->get('db'), $sm);
                },
                'Pages' => function ($sm) {
                    return new Model\Pages($sm->get('db'), $sm);
                },
                'Contacts' => function ($sm) {
                    return new Model\Contacts($sm->get('db'), $sm);
                },
                'ProductReviews' => function ($sm) {
                    return new Model\ProductReviews($sm->get('db'), $sm);
                },
                'UrlIds' => function ($sm) {
                    return new Model\UrlIds($sm->get('db'), $sm);
                },
                'Blocks' => function ($sm) {
                    return new Model\Blocks($sm->get('db'), $sm);
                },
                'BlockProducts' => function ($sm) {
                    return new Model\BlockProducts($sm->get('db'), $sm);
                },
                'ProductHasFields' => function ($sm) {
                    return new Model\ProductHasFields($sm->get('db'), $sm);
                },
                'ProductSizes' => function ($sm) {
                    return new Model\ProductSizes($sm->get('db'), $sm);
                },
                'ProductHasSizes' => function ($sm) {
                    return new Model\ProductHasSizes($sm->get('db'), $sm);
                },
                'ProductColors' => function ($sm) {
                    return new Model\ProductColors($sm->get('db'), $sm);
                },
                'ProductHasColors' => function ($sm) {
                    return new Model\ProductColors($sm->get('db'), $sm);
                },                
                'Vouchers' => function ($sm) {
                    return new Model\Vouchers($sm->get('db'), $sm);
                },               
                'UserActivations' => function ($sm) {
                    return new Model\UserActivations($sm->get('db'), $sm);
                },               
                'EmailLogs' => function ($sm) {
                    return new Model\EmailLogs($sm->get('db'), $sm);
                },               
            ),
        );
    }

}
