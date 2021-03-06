<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

use Application\Lib\Log;
use Application\Lib\Auth;
use Application\Lib\Arr;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

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
        $siteName = 'Admin';
        
        // Set main layout
        switch ($controller) {
            case 'auth':
            case 'page':
                $e->getTarget()->layout('admin/page');
                break;
            default:
                $e->getTarget()->layout('admin/layout');
        }
        
        if (!$sm->get('auth')->hasIdentity() && $action != 'login') {            
            return $e->getTarget()->redirect()->toRoute(
                'admin/page', 
                array(
                    'action' => 'login'
                )
            );
        }
 
        $e->getTarget()->layout()->setVariable('AppUI', $sm->get('auth')->getIdentity());
        $e->getTarget()->layout()->setVariable('controller', $controller);
        $e->getTarget()->layout()->setVariable('action', $action);
        
        // Set layout title
        // Getting the view helper manager from the application service manager
        $viewHelperManager = $sm->get('viewHelperManager');
        
        // Getting the headTitle helper from the view helper manager
        $headTitleHelper = $viewHelperManager->get('headTitle');

        // Setting a separator string for segments
        $headTitleHelper->setSeparator(' - ');
        
        $navigation = $sm->get('navigation');
        $find = $navigation->findBy('id', "admin_{$controller}_{$action}");
        if (!empty($find)) {
            $headTitleHelper->append($find->getLabel());
        }
        $headTitleHelper->append($module);
        //$headTitleHelper->append($siteName);        
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
