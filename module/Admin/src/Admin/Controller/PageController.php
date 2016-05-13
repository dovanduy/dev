<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Application\Lib\Util;
use Application\Lib\Api;
use Admin\Form\Auth\LoginForm;

class PageController extends AppController
{
    public function indexAction()
    {       
        $username = 'yougo';
        $password = 'yougo';
        //$username = 'localhost';
        //$password = '';
        $database = 'yougo';
        $host = 'localhost';
        
        $connection = new \Mongo("mongodb://admin:admin@127.0.0.1/local");
        
        //$con = new \Mongo("mongodb://{$username}:{$password}@{$host}"); // Connect to Mongo Server
        //$db = $con->selectDB($database); // Connect to Database
        d($connection, 1);

        //$a = Util::cryptPassword('123456'); p($a, 1);
        //p(Util::verifyPassword('123456', $a['salt']));
        return $this->getViewModel();
    }
    
    public function loginAction()
    {
        $form = new LoginForm();
        $form->setController($this)
             ->create();
        
        // process login
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {    
                $auth = $this->getServiceLocator()->get('auth');
                if ($auth->authenticate($post['email'], $post['password'])) {                    
                    return $this->redirect()->toRoute('admin');
                } else {
                    $this->addErrorMessage('Login fail, please try again');
                }
            }
        }
        return $this->getViewModel(array(
                'form' => $form
            )
        );
    }
    
    public function logoutAction()
    {
        $auth = $this->getServiceLocator()->get('auth');
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
            return $this->redirect()->toRoute(
                'admin/page', 
                array(
                    'action' => 'login'
                )                              
            );
        }
    }
    
}
