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
use Web\Form\User\ForgetPasswordForm;
use Web\Form\User\NewPasswordForm;
use Web\Form\Auth\LoginForm;
use Web\Module as WebModule;

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
                $registerVoucher = WebModule::getConfig('vouchers.register');
                if (!empty($registerVoucher)) {
                    $post['generate_voucher'] = 1; 
                    $post['voucher_amount'] = $registerVoucher['amount']; 
                    $post['voucher_type'] = $registerVoucher['type']; 
                    $post['voucher_expired'] = $registerVoucher['expired']; 
                    $post['send_email'] = $registerVoucher['send_email']; 
                }                
                $id = Api::call('url_users_add', $post);
                if (Api::error()) {                    
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $auth = $this->getServiceLocator()->get('auth');
                    if ($auth->authenticate($post['email'], $post['password'], 'web')) {                        
                        $this->addSuccessMessage('Account registed successfully');
                        return $this->redirect()->toRoute('web');
                    } 
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
        $backUrl = $this->params()->fromQuery('backurl', '/');
        
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
                    return $this->redirect()->toUrl($backUrl);
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
    
    public function forgetpasswordAction()
    {   
        $form = new ForgetPasswordForm();
        $form->setAttribute('class', 'form-horizontal')
            ->setController($this)
            ->create();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {    
                $post['new_password_url'] = $this->url()->fromRoute('web/newpassword');
                $ok = Api::call('url_users_forgotpassword', $post);
                if (Api::error()) {                    
                    $this->addErrorMessage($this->getErrorMessage());
                } else {                    
                    $this->addSuccessMessage('Please check email to receive change password link');
                    return $this->redirect()->toRoute('web');                     
                }
            }
        }              
        return $this->getViewModel(array(
                'form' => $form,                
            )
        );
    }
    
    public function newpasswordAction()
    {   
        $token = $this->params()->fromRoute('token', '');
        if (empty($token)) {
            $this->notFoundAction();
        }
        $form = new NewPasswordForm();
        $form->setAttribute('class', 'form-horizontal')
            ->setController($this)
            ->create();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {                
                $post['token'] = $token;
                $ok = Api::call('url_users_updatenewpassword', $post);
                if (Api::error()) {                    
                    $this->addErrorMessage($this->getErrorMessage());
                } else {                    
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute('web');                     
                }
            }
        }              
        return $this->getViewModel(array(
                'form' => $form,                
            )
        );
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
        $_id = 'c595110183798d10cf5ba3a4';
        $_id = '4a42d6d4f63ab3bd954fbaea';
        $order = Api::call('url_productorders_detail', array('_id' => $_id));              
        if (!empty($order['user_email'])) {                  
            $order['user_email'] = 'thailvn@gmail.com';
            $mail = $this->getServiceLocator()->get("Mail");     
            $viewModel = new \Zend\View\Model\ViewModel(array('data' => $order));
            $viewModel->setTemplate('email/order');
            $mail->setTo($order['user_email']);                                         
            $mail->setSubject(sprintf('%s DA NHAN DUOC DON HANG %s', $order['website_url'], $order['code']));
            $mail->setBody($viewModel);
            $mail->send();
            echo 'OK';
            exit;
        }
        echo 'Fail';
        /*
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
        * 
        */        
        exit;
    }
    
    public function testapiAction()
    { 
        $detail = Api::call('url_admins_login', array(
            'email' => 'root@gmail.com',
            'password' => '123456',
            'debug' => 1
        ));
        p($detail, 1);
        exit;
    }
    
}
