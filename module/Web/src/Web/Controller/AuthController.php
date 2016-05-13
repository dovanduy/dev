<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Admin\Form\Auth\LoginForm;

class AuthController extends AppController
{
    public function indexAction()
    {
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
            $data = (array) $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {    
                $auth = $this->getServiceLocator()->get('auth');
                if ($auth->authenticate($data['email'], $data['password'])) {                    
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
    
}
