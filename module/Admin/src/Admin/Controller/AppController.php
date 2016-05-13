<?php

namespace Admin\Controller;

use Application\Controller\AbstractAppController;
use Zend\View\Model\ViewModel;

class AppController extends AbstractAppController {

    public function __construct() { 
        parent::__construct();
    }

    public function getViewModel($variables = array()) {
        $module = $this->getModuleName('admin');
        $controller = $this->getControllerName();
        $action = $this->getActionName();   
        $view = new ViewModel($variables);
           
        $resolver = $this->getServiceLocator()->get('Zend\View\Resolver\TemplatePathStack');
        if (!$resolver->resolve("{$module}/{$controller}/{$action}.phtml")
            && $resolver->resolve("{$module}/common/{$action}.phtml")) {
            $view->setTemplate("{$module}/common/{$action}.phtml");
        }
        return $view;
    }
    
    public function getViewHelper($helperName)
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
    }

    public function setBreadcrumbLabel($pageId, $label) {  
        $find = $this->getServiceLocator()->get('navigation')->findBy('id', $pageId);
        if (!empty($find)) {
            $find->setLabel($label);
        }
    }

}
