<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Application\Lib\Arr;
use Admin\Lib\Api;
use Application\Model\Images;
use Application\Model\LocaleStates;
use Application\Model\LocaleCities;
use Admin\Form\User\SearchForm;
use Admin\Form\User\ListForm;
use Admin\Form\User\AddForm;
use Admin\Form\User\UpdateForm;
use Admin\Form\User\PasswordForm;
use Admin\Form\User\AddressListForm;
use Admin\Form\User\AddAddressForm;
use Admin\Form\User\ProductOrderListForm;

class UsersController extends AppController
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
            'sort' => 'updated-desc',
            
        ));      
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            // on/off users.active
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_users_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
        }
        
        // create search form
        $searchForm = new SearchForm();         
        if (!empty($param['country_code'])) {
            // set data for dropdown state_code
            $searchForm->setElementOptions(array(
                'state_code' => array(
                    'value_options' => LocaleStates::getAll($param['country_code'])
                )
            ));
            if (!empty($param['state_code'])) {
                // set data for dropdown city_code
                $searchForm->setElementOptions(array(
                    'city_code' => array(
                        'value_options' => LocaleCities::getAll($param['state_code'], $param['country_code'])
                    )
                ));
            }
        }
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);
        
        // create list form
        $listForm = new ListForm();
        $listForm   ->setController($this)
                    ->setDataset(Api::call('url_users_lists', $param))
                    ->create();
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
     /**
     * Add a User
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
                $id = Api::call('url_users_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/users', 
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
     * Update place information
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
            'url_users_detail', 
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
                if (!empty($data['image_id'])) {
                    $data['url_image'] = Images::getUrl($data['image_id'], 'users', true);
                }
                // set data for dropdown address_id
                $addressList = array();
                foreach ($data['addresses'] as $address) {
                    $addressList[$address['address_id']] = array(
                        $address['name']
                    );                    
                    $item = array();
                    if (!empty($address['country_name'])) {
                        $item[] = $address['country_name']; 
                    }
                    if (!empty($address['state_name'])) {
                        $item[] = $address['state_name']; 
                    }
                    if (!empty($address['city_name'])) {
                        $item[] = $address['city_name']; 
                    }
                    if (!empty($address['street'])) {
                        $item[] = $address['street']; 
                    }
                    $addressList[$address['address_id']] = $address['name'];
                    if (!empty($item)) {
                        $addressList[$address['address_id']] .= ' (' . implode(', ', $item) . ')';
                    }
                }
                $form->setElementOptions(array(
                    'address_id' => array(
                        'value_options' => 
                            array('' => '--Select one--') +
                           $addressList
                    )
                ));                    
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create()
                     ->bindData($data);
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();                                          
                    if (!empty($post['remove']['url_image'])) {
                        $post['image_id'] = 0;
                    }
                    $form->setData($post);
                    if ($form->isValid()) {                    
                        Api::call('url_users_update', $post);  
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
                
            case 'address':
                // create add/update form
                $form = new AddAddressForm();                                            
                $addresses = !empty($data['addresses']) ? $data['addresses'] : array();
                $addressId = $this->params()->fromQuery('addressid', '');
                $address = array();
                if ($addressId) {                    
                    $address = Arr::filter($addresses, '_id', $addressId, false, false);  
                    $address = !empty($address[0]) ? $address[0] : array();
                    // not found data
                    if (empty($address)) {
                        return $this->notFoundAction();
                    }                     
                }
                        
                $request = $this->getRequest();
                if ($request->isPost()) {
                    if (!empty($request->getPost('country_code'))) {
                        $address['country_code'] = $request->getPost('country_code');
                        if (!empty($request->getPost('state_code'))) {
                            $address['state_code'] = $request->getPost('state_code');
                        }
                    }
                }
                if (empty($address['country_code'])) {
                    $address['country_code'] = \Application\Module::getConfig('general.default_country_code');
                }
                if (!empty($address['country_code'])) {
                    // set data for dropdown state_code
                    $form->setElementOptions(array(
                        'state_code' => array(
                            'value_options' => LocaleStates::getAll($address['country_code'])
                        )
                    ));
                    if (!empty($address['state_code'])) {
                        // set data for dropdown city_code
                        $form->setElementOptions(array(
                            'city_code' => array(
                                'value_options' => LocaleCities::getAll($address['state_code'], $address['country_code'])
                            )
                        ));
                    }
                }  
                
                $form->setController($this)
                    ->setAttribute('id', 'addressForm')
                    ->create()
                    ->bindData($address);
                    
                // save form
                if ($request->isPost()) {                    
                    $post = (array) $request->getPost();     
                    
                    if ($request->isXmlHttpRequest()) {
                        // on/off users.active
                        if (isset($post['_id']) && isset($post['value'])) {       
                            Api::call(
                                'url_addresses_onoff', 
                                $post
                            );
                            die('OK');
                        }
                        
                        // add address
                        $form->setData($post); 
                        if (!$form->isValid() && !empty($form->getMessages())) {
                            die($this->getErrorMessageForAjax($form->getMessages()));
                        } else {
                            if (!empty($post['_id'])) {
                                Api::call('url_addresses_update', $post);
                            } else {
                                $post['user_id'] = $data['user_id'];
                                Api::call('url_addresses_add', $post);
                            }
                            if (empty(Api::error())) { 
                                $result['status'] = 'OK';
                                $result['message'] = 'Data saved successfully';
                                die(json_encode($result));
                            }
                            die($this->getErrorMessageForAjax());                              
                        }    
                    }
                }
                
                $listForm = new AddressListForm();
                $listForm->setController($this)
                    ->setDataset($addresses)
                    ->create();               
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
                        Api::call('url_users_updatepassword', $post);  
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
            
            case 'productorder': 
                $param = $this->getParams(array(
                    'page' => 1,
                    'limit' => \Application\Module::getConfig('general.default_limit'),
                    'sort' => 'created-desc',            
                    'user_id' => $data['user_id']
                ));
                // create list form
                $listForm = new ProductOrderListForm();
                $listForm   ->setController($this)
                            ->setDataset(Api::call('url_productorders_lists', $param))
                            ->create();
                break;    
            default:     
                
        }
        if (Api::error() || $this->getErrorMessage()) {
            $this->addErrorMessage($this->getErrorMessage());
        }
        
        return $this->getViewModel(array(
                'form' => isset($form) ? $form : null,
                'listForm' => isset($listForm) ? $listForm : null,
            )
        );       
    }  
    
}
