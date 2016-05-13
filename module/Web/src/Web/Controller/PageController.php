<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Application\Lib\Util;
use Application\Lib\Cache;

use Web\Lib\Api;
use Web\Form\User\RegisterForm;
use Web\Form\Auth\LoginForm;

class PageController extends AppController
{
    public function indexAction()
    {    
        return $this->getViewModel();
    }
    
    public function signupAction()
    {
        $AppUI = $this->getLoginInfo();
        if (!empty($AppUI)) {
            return $this->redirect()->toRoute('web');
        }
        
        $form = new RegisterForm();  
        $form->setAttribute('class', 'form-horizontal')
            ->setController($this)
            ->create('post');
        
        // send form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();         
            $form->setData($post);         
            if ($form->isValid()) {
                $id = Api::call('url_users_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'web/page', 
                        array(
                            'action' => 'signup'
                        )                        
                    );
                }
            }            
        }
        return $this->getViewModel(array(
               'form' => $form
            )
        );
    }
    
    public function loginAction()
    {
        $AppUI = $this->getLoginInfo();
        if (!empty($AppUI)) {
            return $this->redirect()->toRoute('web');
        } 
        
        $form = new LoginForm();
        $form->setAttribute('class', 'form-horizontal')
            ->setController($this)
            ->create();
        
        // process login
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {    
                $auth = $this->getServiceLocator()->get('auth');
                if ($auth->authenticate($post['email'], $post['password'], 'web')) {                    
                    if (isset($post['remember']) && $post['remember'] == 1) {    
                        $remember = serialize(array(
                            'email' => $post['email'],
                            'password' => $post['password'],
                        ));
                        $cookie = new \Zend\Http\Header\SetCookie('remember', $remember, time() + 365 * 60 * 60 * 24, '/');
                        $this->getResponse()->getHeaders()->addHeader($cookie);
                    } else {
                        $headCookie = $request->getHeaders()->get('Cookie');  
                        if ($headCookie->offsetExists('remember')) {
                            $cookie = new \Zend\Http\Header\SetCookie('remember', '', time() - 365 * 60 * 60 * 24, '/');                
                            $this->getResponse()->getHeaders()->addHeader($cookie);
                        }
                    }
                    return $this->redirect()->toRoute('web');
                } else {
                    $this->addErrorMessage('Invalid Email or password. Please try again');
                }
            }
        }              
        return $this->getViewModel(array(
                'form' => $form,                
            )
        );
    }
    
    public function logoutAction()
    {
        $request = $this->getRequest();
        $auth = $this->getServiceLocator()->get('auth');
        if ($auth->hasIdentity()) {
            $headCookie = $request->getHeaders()->get('Cookie');  
            if ($headCookie->offsetExists('remember')) {
                $cookie = new \Zend\Http\Header\SetCookie('remember', '', time() - 365 * 60 * 60 * 24, '/');                
                $this->getResponse()->getHeaders()->addHeader($cookie);
            }
            $auth->clearIdentity();            
            return $this->redirect()->toRoute('web');
        }
    }
    
    public function fbloginAction()
    {
        $backUrl = $this->params()->fromQuery('backurl', '/');
        $request = $this->getRequest();        
        if ($request->isPost()) {
            $post = (array) $request->getPost();
            $auth = $this->getServiceLocator()->get('auth');
            if ($auth->authenticate(null, null, 'facebook', $post)) { 
                $result = array(
                    'error' => 0,
                    'message' => 'FbLogin success',
                    'backUrl' => $backUrl,
                );
            } else {
                $result = array(
                    'error' => 1,
                    'message' => 'Invalid Email or password. Please try again',
                );               
            }           
            die(\Zend\Json\Encoder::encode($result));
        }      
        exit;
    }
    
     /**
     * Delete cache
     *
     * @return Zend\View\Model
     */
    public function deletecacheAction()
    { 
        Cache::flush();
        $this->addSuccessMessage('Cached data delected successfully');
        return $this->redirect()->toRoute('web');
    }   
    
    /**
     * Delete cache
     *
     * @return Zend\View\Model
     */
    public function testmailAction()
    { 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\RendererInterface');
        $mail = $this->getServiceLocator()->get("Mail");        
        $viewModel = new \Zend\View\Model\ViewModel(
            array(
                'name' => 'Thai Lai',
                'email' => 'thailvn@gmail.com',
                'message' => 'OK',
            )
        );      
        $viewModel->setTemplate('email/order');
        $content = $renderer->render($viewModel);        
        $viewLayout = new \Zend\View\Model\ViewModel(array('content' => $content));
        $viewLayout->setTemplate('email/layout');  
        $mail->setTo('thailvn@gmail.com'); 
        $mail->setSubject('Test'); 
        $mail->setBody($viewLayout);
        d($mail->send());        
        exit;
    }   
    
}
