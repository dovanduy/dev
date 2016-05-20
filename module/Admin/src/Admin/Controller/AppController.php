<?php

namespace Admin\Controller;

use Application\Controller\AbstractAppController;
use Zend\View\Model\ViewModel;
use Admin\Lib\Api;

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

    public function getErrorMessage($mapErrors = array(), $errors = array()) {  
        $messages = array(); 
        if (empty($errors)) {
            $errors = Api::error();
        }
        if (!empty($errors)) {
            foreach ($errors as $error) { 
                if (!empty($mapErrors)) {
                    foreach ($mapErrors as $mapError) {
                        if ($error['field'] == $mapError['field'] && $error['code'] == $mapError['code']) {
                            $messages[] = $mapError['message'];
                        }
                    }
                } else {
                    $messages[] = $error['message'];
                }
            } 
        }
        return !empty($messages) ? implode('<br/>', $messages) : '';
    }
    
    public function getErrorMessageForAjax($validateErrors = array(), $mapErrors = array()) {  
        $errorMessages = array();
        if (!empty(Api::error())) {
            foreach (Api::error() as $error) { 
                if (empty($errorMessages[$error['field']])) {
                    $errorMessages[$error['field']] = array();
                }
                if (!empty($mapErrors)) {
                    foreach ($mapErrors as $mapError) {
                        if ($error['field'] == $mapError['field'] && $error['code'] == $mapError['code']) {
                            $errorMessages[$mapError['field']][] = $mapError['message'];
                        }
                    }
                } else {
                    $errorMessages[$error['field']][] = $error['message'];
                }
            } 
        }
        if (!empty($validateErrors)) {
            $errorMessages = array_replace_recursive($validateErrors, $errorMessages);
        }
        $errors = array();
        foreach ($errorMessages as $field => $messages) {
            $errors[$field] = '<ul>';
            foreach ($messages as $message) {
                $message = $this->translate($message);
                $errors[$field] .= "<li class='error'>{$message}</li>";
            }
            $errors[$field] .= '</ul>';
        }
        return json_encode($errors);
    }
}
