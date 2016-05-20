<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Lib\Log;
use Application\Lib\Api;
use Application\Lib\Arr;

abstract class AbstractAppController extends AbstractActionController {   
            
    public $translator;
    public $controller;
    public $action;
    
    public function __construct()
    {
        
    }
  
    public function getLoginInfo() {
        $auth = $this->getServiceLocator()->get('auth');
        if ($auth->hasIdentity()) { 
            return $auth->getIdentity();   
        }
        return null;
    }
    
    public function getViewPath() {
        $moduleName = $this->getModuleName();
        $resolver = $this->getServiceLocator()->get('Zend\View\Resolver\TemplatePathStack');
        $paths = $resolver->getPaths()->toArray();
        foreach ($paths as $path) {
            preg_match('/' . $moduleName . '/i', $path, $match);
            if (!empty($match[0])) {
                return $path;
            }
        }
    }
    
    public function getModuleName($default = 'application') {       
        return strtolower(reset(explode('\\', $this->getEvent()->getRouteMatch()->getParam('controller', $default))));
    }
    
    public function getControllerName() {
        return strtolower(array_pop(explode('\\', $this->getEvent()->getRouteMatch()->getParam('controller', 'index'))));
    }
    
    public function getActionName() {
        return strtolower($this->getEvent()->getRouteMatch()->getParam('action', 'index')); 
    }
    
    public function getParams($default = array()) {        
        $params = array_merge(
            $this->params()->fromQuery(),
            $this->params()->fromPost()
        );
        if (!empty($default)) {
            foreach ($default as $name => $value) {
                if (!isset($params[$name])) {
                    $params[$name] = $value;
                }
            }            
        }
        return $params;
    }
    
    public function translate() {      
        $args = func_get_args();
        if (count($args) < 1) {
            return '';
        }
        $args[0] = $this->translator->translate($args[0]);
        return call_user_func_array('sprintf', $args);
    }
    
    public function addSuccessMessage($message) {      
        $this->flashMessenger()->addSuccessMessage($message);       
    }
    
    public function addErrorMessage($message) {      
        $this->flashMessenger()->addErrorMessage($message);
    }  
    
}
