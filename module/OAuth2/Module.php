<?php

namespace OAuth2;

use Application\Lib\Log;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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

    // getAutoloaderConfig() and getConfig() methods here
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
                               
            ),
        );
    }

}
