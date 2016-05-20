<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Admin\Lib\Api;
use Application\Model\Images;
use Admin\Form\Admin\SearchForm;
use Admin\Form\Admin\ListForm;
use Admin\Form\Admin\AddForm;
use Admin\Form\Admin\UpdateForm;
use Admin\Form\Admin\ProfileForm;
use Admin\Form\Admin\PasswordForm;
use Application\Model\LocaleStates;
use Application\Model\LocaleCities;

class AdminsController extends AppController
{    
    /**
     * construct
     * 
     */
    public function __construct()
    {        
        parent::__construct();        
    }
    
    /**
     * Place list
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    {
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit'),
            'sort' => 'name-asc',
        ));      
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            // on/off news.active
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_admins_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
        }
        
        // create search form
        $searchForm = new SearchForm();  
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);
        
        // create list form
        $listForm = new ListForm();
        $listForm   ->setController($this)
                    ->setDataset(Api::call('url_admins_lists', $param))
                    ->create();
        
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
     /**
     * Add a Admin
     *
     * @return Zend\View\Model
     */
    public function addAction()
    {
        // create add/edit form
        $form = new AddForm();        
        $form->setAttribute('enctype','multipart/form-data')
             ->setController($this)
             ->create();
        
        // save form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();         
            $form->setData($post);         
            if ($form->isValid()) { 
                $id = Api::call('url_admins_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/admins', 
                        array(
                            'action' => 'update', 
                            'id' => $id
                        )                        
                    );
                }
            }
        }
        
        return $this->getViewModel(array(
                'form' => $form,
            )
        );
    }
    
    /**
     * Update admin information
     *
     * @return Zend\View\Model
     */
    public function updateAction()
    { 
        $request = $this->getRequest();
        
        $id = $this->params()->fromRoute('id', 0);
        $tab = $this->params()->fromQuery('tab', '');
        $backUrl = $this->params()->fromQuery('backurl', '');
       
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }
        
        // get news detail             
        $data = Api::call(
            'url_admins_detail', 
            array(
                '_id' => $id, 
            )
        );
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        
        switch ($tab) {
            case '':
                // create edit form
                $form = new UpdateForm();    
                if ($request->isPost()) {
                    if (!empty($request->getPost('country_code'))) {
                        $data['country_code'] = $request->getPost('country_code');
                        if (!empty($request->getPost('state_code'))) {
                            $data['state_code'] = $request->getPost('state_code');
                        }
                    }
                }
                if (!empty($data['country_code'])) {
                    // set data for dropdown state_code
                    $form->setElementOptions(array(
                        'state_code' => array(
                            'value_options' => LocaleStates::getAll($data['country_code'])
                        )
                    ));
                    if (!empty($data['state_code'])) {
                        // set data for dropdown city_code
                        $form->setElementOptions(array(
                            'city_code' => array(
                                'value_options' => LocaleCities::getAll($data['state_code'], $data['country_code'])
                            )
                        ));
                    }
                }
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create();
                if (!empty($data['image_id'])) {
                    $data['url_image'] = Images::getUrl($data['image_id'], 'admins', true);
                }
                $form->bindData($data);
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();                                          
                    if (!empty($post['remove']['url_image'])) {
                        $post['image_id'] = 0;
                    }
                    $form->setData($post);
                    if ($form->isValid()) {                       
                        Api::call('url_admins_update', $post);  
                        if (empty(Api::error())) {
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                        }
                        return $this->redirect()->toUrl($request->getRequestUri());
                    }                    
                }                
                break;
                
            case 'password': 
                // create password form
                $data['password'] = ''; 
                $form = new PasswordForm();                  
                $form->setController($this)                    
                     ->create()
                     ->bindData($data);
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();                
                    $form->setData($post);     
                    if ($form->isValid()) {                        
                        Api::call('url_admins_updatepassword', $post);  
                        if (empty(Api::error())) {
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                        }
                        return $this->redirect()->toUrl($request->getRequestUri());
                    }                    
                }                
                break;  
                
            default:     
                
        }
        if (Api::error() || $this->getErrorMessage()) {
            $this->addErrorMessage($this->getErrorMessage());
        }
        
        return $this->getViewModel(array(
                'form' => $form,
            )
        );       
    } 
    
    /**
     * Update admin profile
     *
     * @return Zend\View\Model
     */
    public function profileAction()
    { 
        $request = $this->getRequest();
      
        $AppUI = $this->getLoginInfo();
        if (empty($AppUI)) {
            return $this->notFoundAction();
        }
        
        $id = $AppUI->_id;
        $tab = $this->params()->fromQuery('tab', '');
        $backUrl = $this->params()->fromQuery('backurl', '');
        
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }
        
        // get news detail             
        $data = Api::call(
            'url_admins_detail', 
            array(
                '_id' => $id, 
            )
        );
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        
        switch ($tab) {
            case '':
                // create edit form
                $form = new ProfileForm();    
                if ($request->isPost()) {
                    if (!empty($request->getPost('country_code'))) {
                        $data['country_code'] = $request->getPost('country_code');
                        if (!empty($request->getPost('state_code'))) {
                            $data['state_code'] = $request->getPost('state_code');
                        }
                    }
                }
                if (!empty($data['country_code'])) {
                    // set data for dropdown state_code
                    $form->setElementOptions(array(
                        'state_code' => array(
                            'value_options' => LocaleStates::getAll($data['country_code'])
                        )
                    ));
                    if (!empty($data['state_code'])) {
                        // set data for dropdown city_code
                        $form->setElementOptions(array(
                            'city_code' => array(
                                'value_options' => LocaleCities::getAll($data['state_code'], $data['country_code'])
                            )
                        ));
                    }
                }
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create();
                if (!empty($data['image_id'])) {
                    $data['url_image'] = Images::getUrl($data['image_id'], 'admins', true);
                }
                $form->bindData($data);
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();                                          
                    if (!empty($post['remove']['url_image'])) {
                        $post['image_id'] = 0;
                    }
                    $form->setData($post);
                    if ($form->isValid()) {                       
                        Api::call('url_admins_update', $post);  
                        if (empty(Api::error())) {
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                        }
                        return $this->redirect()->toUrl($request->getRequestUri());
                    }                    
                }                
                break;
                
            case 'password': 
                // create password form
                $data['password'] = ''; 
                $form = new PasswordForm();                  
                $form->setController($this)                    
                     ->create()
                     ->bindData($data);
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();                
                    $form->setData($post);     
                    if ($form->isValid()) {                        
                        Api::call('url_admins_updatepassword', $post);  
                        if (empty(Api::error())) {
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                        }
                        return $this->redirect()->toUrl($request->getRequestUri());
                    }                    
                }                
                break;  
                
            default:     
                
        }
        if (Api::error() || $this->getErrorMessage()) {
            $this->addErrorMessage($this->getErrorMessage());
        }
        
        return $this->getViewModel(array(
                'form' => $form,
            )
        );       
    }  
    
}
