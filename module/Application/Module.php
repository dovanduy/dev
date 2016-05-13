<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Lib\Arr;
use Application\Lib\Mail;
use Application\Lib\Util;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Cache\StorageFactory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ConsoleUsageProviderInterface,
    Zend\Console\Adapter\AdapterInterface as Console;

class Module implements AutoloaderProviderInterface,
    ConfigProviderInterface,
    ConsoleUsageProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();            
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);    
    }

    public function getConfig($name = '', $default = null)
    {
        $module = include __DIR__ . '/config/module.config.php';
        $api = include __DIR__ . '/config/api.config.php';
        $config = $module + $api;
        
        if (isset($_SERVER['SERVER_NAME']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SERVER['SERVER_NAME'] . '.php')) {
            $domainConfig = include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SERVER['SERVER_NAME'] . '.php';
            $config = array_replace_recursive($config, $domainConfig);
        }
        if (Util::isMobile() && file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mobile.config.php')) {
            $mobileConfig = include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mobile.config.php';
            $config = array_replace_recursive($config, $mobileConfig);
        }
//        
//        if (isset($_SERVER['SERVER_NAME'])) {
//            $serverName = explode('.', $_SERVER['SERVER_NAME']);
//            if ($_SERVER['SERVER_NAME'] !== 'localhost'
//                && count($serverName) >= 2) {
//                if (count($serverName) >= 3 && in_array($serverName[0], array('admin', 'api'))) {
//                    $domain = $serverName[1] . '.' . $serverName[2];
//                } else {
//                    $domain = $serverName[0] . '.' . $serverName[1];
//                }
//                $file = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $domain . '.php';
//                if (file_exists($file)) {
//                    $domainConfig = include $file;
//                    $config = array_replace_recursive($config, $domainConfig);
//                }            
//            }       
//        }       
        if (!empty($name)) {
            return Arr::get($config, $name, $default);
        }
        return $config;
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,   
                ),
            ),
        );
    }
    
    // Add this method:
    public function getServiceConfig() {
        return array(
            'factories' => array(
                "Mail" => function($sm) {
                    $mail = new Mail();
                    $mail->setRenderer($sm->get("ViewRenderer"));
                    $config = \Application\Module::getConfig('email.smtp');                    
                    $smtpOptions = new \Zend\Mail\Transport\SmtpOptions();  
                    $smtpOptions
                        ->setHost($config['host'])
                        ->setPort($config['port'])
                        ->setConnectionClass('login')
                        ->setName($config['name'])
                        ->setConnectionConfig(array(
                            'username' => $config['username'],
                            'password' => $config['password'],
                            'ssl' => $config['ssl'],
                        ));
                    $transporter = new \Zend\Mail\Transport\Smtp($smtpOptions);
                    $mail->setTransporter($transporter);
//                    $mail->setFrom(
//                        \Application\Module::getConfig('from_email'), 
//                        \Application\Module::getConfig('from_name')
//                    );
                    return $mail;
                }
            ),
        );
    }
    
    public function getConsoleUsage(Console $console) {
        return array(
            // Describe available commands
            'import products [--verbose|-v] <doname>' => 'Get Process already happen',
            // Describe expected parameters
            array('doname', 'Process Name'),
            array('--verbose|-v', '(optional) turn on verbose mode'),
        );
    }

}
