<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web;

use Zend\View\Model\ViewModel;
use Application\Lib\Log;
use Application\Lib\Auth;
use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;
use Web\Module as WebModule;


class Module 
{    
    /**
     * Boot module
     * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $translator = $e->getApplication()->getServiceManager()->get('translator');         
        $translator->setLocale('vi_VN');
        AbstractValidator::setDefaultTranslator($translator);
        
        $e->getApplication()->getServiceManager()
                            ->get('ViewHelperManager')
                            ->get('translate')
                            ->setTranslator($translator);
       
    }
   
     /**
     * Init module
     * @param  Zend\ModuleManager\ModuleManager $mm ModuleManager
     * @return void
     */
    public function init(\Zend\ModuleManager\ModuleManager $mm)
    {
        $mm->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__,
            MvcEvent::EVENT_DISPATCH, function($e) {         
                $this->onDispatch($e);
            }         
        );        
    }   
    
     /**
     * Get config of module
     * @param  string $name Config name
     * @param  mixed $default Default value
     * @return mixed
     */
    public function getConfig($name = '', $default = null)
    {
        $config = include __DIR__ . '/config/module.config.php';  
        if (isset($_SERVER['SERVER_NAME']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SERVER['SERVER_NAME'] . '.php')) {
            $domainConfig = include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SERVER['SERVER_NAME'] . '.php';
            $config = array_replace_recursive($config, $domainConfig);
        }
        if (Util::isMobile() && file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mobile.config.php')) {
            $mobileConfig = include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mobile.config.php';
            $config = array_replace_recursive($config, $mobileConfig);
        }       
        if (!empty($name)) {
            return Arr::get($config, $name, $default);
        }
        return $config;
    }
    
    public function getValidatorConfig($name = '', $default = null)
    {
        $config = include __DIR__ . '/config/validators.config.php';  
        if (!empty($name)) {
            return Arr::get($config, $name, $default);
        }
        return $config;
    }
    
    /**
     * Autoload config
     * @return void
     */
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
    
    /**
     * Set layout, ...
     * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function onDispatch($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $matches = $e->getRouteMatch();
        $action = $matches->getParam('action');
        $controller = strtolower(array_pop(explode('\\', $matches->getParam('controller'))));
        $module = __NAMESPACE__;
                
        // Set main layout         
        $e->getTarget()->layout('web/layout');
        
        $AppUI = $sm->get('auth')->getIdentity();        
        $request = $e->getTarget()->getRequest();       
        if (empty($AppUI)) {
            $headCookie = $request->getHeaders()->get('Cookie'); 
            $remember = isset($headCookie->remember) ? unserialize($headCookie->remember) : array();
            if (!empty($remember) && $sm->get('auth')->authenticate($remember['email'], $remember['password'], 'web')) { 
                $AppUI = $sm->get('auth')->getIdentity();                
            }
        }
          
        $website = \Web\Model\Websites::getDetail(); 
      
        if (!$request->isXmlHttpRequest()) { // not ajax request             
            $headerMenus = \Web\Model\Menus::getSubMenu2($website['menus'], $lastLevel = array(), 0, 0, $type = 'header');
            $footerMenus = \Web\Model\Menus::getSubMenu2($website['menus'], $lastLevel = array(), 0, 0, $type = 'footer');
            $e->getTarget()->layout()->setVariable('headerMenus', $headerMenus);
            $e->getTarget()->layout()->setVariable('footerMenus', $footerMenus);            
        }     
        
        // Getting the view helper manager from the application service manager
        $viewHelperManager = $sm->get('viewHelperManager');
        
        // Head Title Setting
        $headTitleHelper = $viewHelperManager->get('headTitle');       
        $headTitleHelper->setSeparator(' - ');
                       
        if (!empty($website['name'])) {
            $headTitleHelper->append($website['name']);
        }
        if (!empty($website['company_name'])) {
            $headTitleHelper->append($website['company_name']);
        }
        // End Head Title Setting
        
        $e->getTarget()->layout()->setVariable('AppUI', $AppUI);                
        $e->getTarget()->layout()->setVariable('controller', $controller);
        $e->getTarget()->layout()->setVariable('action', $action);
        $e->getTarget()->layout()->setVariable('website', $website);
    }
    
     /** 
     * @return void
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'formElementErrors' => function($sm) {
                    $helper = new \Application\Form\View\Helper\FormElementErrors; 
                    return $helper;
                },
                'requestHelper' => function($sm) {
                    $helper = new \Application\View\Helper\RequestHelper;
                    $request = $sm->getServiceLocator()->get('Request');                    
                    $helper->setRequest($request);
                    return $helper;
                },
                'htmlMenu' => function($sm) {
                    $helper = new \Application\View\Helper\HtmlMenu; 
                    return $helper;
                },                
            ),
        );
    }
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'auth' => function ($sm) {
                    return new Auth();
                },              
            ),
        );
    }
    
}
