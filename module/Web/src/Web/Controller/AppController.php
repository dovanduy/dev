<?php

namespace Web\Controller;

use Zend\View\Model\ViewModel;
use Application\Controller\AbstractAppController;
use Application\Lib\Util;
use Web\Lib\Api;
use Web\Module as WebModule;

class AppController extends AbstractAppController {
    
    private $_hasSetHead = false;
    
    public function __construct() {         
        parent::__construct();        
    }

    public function setHead($data = array()) {
        $this->_hasSetHead = true;
        $website = \Web\Model\Websites::getDetail();
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $defaultData = array(
            'meta_name' => array(
                'X-UA-Compatible' => 'IE=edge',
                //'viewport' => 'width=device-width, initial-scale=1.0',
                'author' => WebModule::getConfig('head_meta.author'),
                'owner' => WebModule::getConfig('head_meta.owner'),
                'distribution' => WebModule::getConfig('head_meta.distribution'),
                'placename' => WebModule::getConfig('head_meta.placename'),
                'copyright' => $this->translate(WebModule::getConfig('head_meta.copyright')),
                'robots' => 'index,follow',
                'description' => $website['meta_description'],
                'keywords' => $website['meta_keyword'],                
            ),
            'meta_property' => array(
                //'fb:admins' => WebModule::getConfig('facebook_admins'),
                'fb:app_id' => WebModule::getConfig('facebook_app_id'),
                'og:type' => 'product',
                'og:title' => '',
                'og:description' => '',
                'og:site_name' => !empty($website['name']) ? $website['name'] : '',
                'og:image' => !empty($website['url_image']) ? $website['url_image'] : '',
                'og:url' => $renderer->serverUrl(true)
            ),
        );
        if (!empty($data['meta_name'])) {
            foreach ($data['meta_name'] as $key => $value) {  
                if (empty($value)) {
                    unset($data['meta_name'][$key]);
                }
            }
        }
        if (!empty($data['meta_property'])) {
            foreach ($data['meta_property'] as $key => $value) {  
                if (empty($value)) {
                    unset($data['meta_property'][$key]);
                }
            }
        }
        $data = array_replace_recursive($defaultData, $data);          
        if (!empty($data['title'])) {
            $renderer->headTitle($data['title']);    
        }
        if (!empty($data['meta_name'])) {
            foreach ($data['meta_name'] as $key => $value) {                
                if (!empty($value)) {
                    if (is_array($value)) {
                        $value = implode(' - ', $value);
                    }
                    $renderer->headMeta()->setName($key, trim($value));
                }
            }      
        }   
        if (!empty($data['meta_property'])) {
            foreach ($data['meta_property'] as $key => $value) {
                if (!empty($value)) {
                    if (is_array($value)) {
                        foreach ($value as $propertyValue) {
                            $renderer->headMeta()->appendProperty($key, $propertyValue);
                        }                        
                    } else {
                        $renderer->headMeta()->setProperty($key, trim($value));
                    }
                }
            }      
        }
        if (!empty($data['link'])) {
            foreach ($data['link'] as $value) {                
                $renderer->headLink()->prependStylesheet($value, 'screen', '', array('rel' => 'image_src'));
            }      
        }
        return $renderer->headMeta();       
    }   
    
    public function getViewModel($variables = array(), $templateName = '') {        
        if ($this->_hasSetHead == false) {
            $this->setHead();
        }
        $AppUI = $this->getLoginInfo();
        if (!empty($AppUI)) {
            $variables['AppUI'] = $AppUI;
        }        
        $variables['isMobile'] = Util::isMobile();       
        $module = $this->getModuleName();
        $controller = $this->getControllerName();
        $action = $this->getActionName();
        $view = new ViewModel($variables);      
        $resolver = $this->getServiceLocator()->get('Zend\View\Resolver\TemplatePathStack');    
        if (empty($templateName)) {
            $templateName = $action;
        }                
        $domain = domain();        
        if (Util::isMobile() && $resolver->resolve("{$module}/{$domain}/mobile/{$controller}/{$templateName}.phtml")) {
            $view->setTemplate("{$module}/{$domain}/mobile/{$controller}/{$templateName}.phtml");           
            return $view;
        }        
        if ($resolver->resolve("{$module}/{$domain}/{$controller}/{$templateName}.phtml")) {
            $view->setTemplate("{$module}/{$domain}/{$controller}/{$templateName}.phtml");           
        } else {
            $view->setTemplate("{$module}/{$controller}/{$templateName}.phtml");
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
    
     public function addBreadcrumbItem($pageId, $option) {  // array(uri, label, active=true/false)
        $find = $this->getServiceLocator()->get('navigation')->findBy('id', $pageId);
        if (!empty($find)) {
            $find->addPage($option);        
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
            if (is_array($messages)) {
                foreach ($messages as $message) {
                    $message = $this->translate($message);
                    $errors[$field] .= "<li class='error'>{$message}</li>";
                }
            } else {
                $message = $this->translate($messages);
                $errors[$field] .= "<li class='error'>{$messages}</li>";
            }
            $errors[$field] .= '</ul>';
        }
        return json_encode($errors);
    }
    
    public function isAdmin()
    {
        $AppUI = $this->getLoginInfo();
        if (!empty($AppUI) && in_array($AppUI->id, \Application\Module::getConfig('admin_user_id'))) {
            return true;
        }
        return false;
    }

}
