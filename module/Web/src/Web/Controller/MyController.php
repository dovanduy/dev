<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Application\Lib\Arr;
use Web\Lib\Api;
use Application\Model\Images;
use Web\Model\LocaleStates;
use Web\Model\LocaleCities;
use Web\Model\Users;
use Web\Form\My\UpdateForm;
use Web\Form\My\PasswordForm;
use Web\Form\My\Password2Form;
use Web\Form\My\AddressListForm;
use Web\Form\My\AddAddressForm;
use Web\Form\My\ProductOrderListForm;
use Web\Form\My\AllOrderListForm;

class MyController extends AppController
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
     * 
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    { 
        $request = $this->getRequest();
        $AppUI = $this->getLoginInfo();
        
        $id = $AppUI->_id;
        $tab = $this->params()->fromQuery('tab', '');
        $backUrl = $this->params()->fromQuery('backurl', '');
       
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }
        $data = Users::getDetail($AppUI->_id);        
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
       
        $addresses = array();
        if (!empty($data['addresses'])) {
            $addressName = \Application\Module::getConfig('address_name');
            foreach ($data['addresses'] as &$address) {
                if ($address['active'] == 1) {
                    if (isset($address['name'])) {
                        //$address['name'] = $this->translate($addressName[$address['name']]);
                    }
                    $item = array();
                    if (!empty($address['street'])) {
                        $item[] = $address['street']; 
                    }
                    if (!empty($address['city_name'])) {
                        $item[] = $address['city_name']; 
                    }
                    if (!empty($address['state_name'])) {
                        $item[] = $address['state_name']; 
                    }
                    if (!empty($address['country_name'])) {
                        $item[] = $address['country_name']; 
                    }
                    $address['address'] = implode(', ', $item);
                    $addresses[] = $address;
                }                
            }    
            unset($address);
        }    
        
        switch ($tab) {
            case '':
                $this->setHead(array(
                    'title' => $this->translate('Profile')
                ));
                $navigationPage = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_my_index');
                if ($navigationPage) {
                    $navigationPage->setLabel('');
                    $navigationPage->addPage(array( 
                        'uri' => '',
                        'label' => 'Profile',
                        'active' => true
                    ));
                }
                // create edit form
                $form = new UpdateForm(); 
                if (!empty($data['image_id'])) {                    
                    $data['url_image'] = Images::getUrl($data['image_id'], 'users');
                }
                // set data for dropdown address_id
                $addressList = array();
                foreach ($addresses as $address) {
                    $addressList[$address['address_id']] = array(
                        $address['name']
                    );                    
                    $item = array();
                    if (!empty($address['street'])) {
                        $item[] = $address['street']; 
                    }
                    if (!empty($address['city_name'])) {
                        $item[] = $address['city_name']; 
                    }
                    if (!empty($address['state_name'])) {
                        $item[] = $address['state_name']; 
                    }
                    if (!empty($address['country_name'])) {
                        $item[] = $address['country_name']; 
                    } 
                    $addressList[$address['address_id']] = $address['name'];
                    if (!empty($item)) {
                        $addressList[$address['address_id']] = implode(', ', $item);
                    }
                }
                $form->setElementOptions(array(
                    'address_id' => array(
                        'value_options' => $addressList
                    )
                ));                    
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create()
                     ->bindData($data);
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();     
                    $post['image_id'] = $data['image_id'];
                    if (!empty($post['remove']['url_image'])) {
                        $post['image_id'] = 0;
                    }
                    $form->setData($post);
                    if ($form->isValid()) {    
                        $post['_id'] = $id;
                        $post['get_login'] = 1;
                        $user = Api::call('url_users_update', $post); 
                        if (empty(Api::error())) {
                            Users::removeCache($AppUI->_id);
                            Images::removeCache($data['image_id'], 'users');
                            $auth = $this->getServiceLocator()->get('auth');                            
                            if (isset($user['password'])) {
                                unset($user['password']);
                            }
                            if (isset($user['hash_password'])) {
                                unset($user['hash_password']);
                            }
                            $user['access_token'] = $AppUI->access_token;
                            $auth->getStorage()->write((object) $user);                           
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
                $this->setHead(array(
                    'title' => $this->translate('My address')
                ));
                $navigationPage = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_my_index');
                if ($navigationPage) {
                    $navigationPage->setLabel('');
                    $navigationPage->addPage(array( 
                        'uri' => '',
                        'label' => 'My address',
                        'active' => true
                    ));
                }
                // create add/update form
                $form = new AddAddressForm(); 
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
                            Users::removeCache($AppUI->_id);
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
                                Users::removeCache($AppUI->_id);
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
                    ->setAttribute('id', 'addressListForm')
                    ->setDataset($addresses)
                    ->create();               
                break;
            
            case 'password': 
                $this->setHead(array(
                    'title' => $this->translate('Change password')
                ));
                $navigationPage = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_my_index');
                if ($navigationPage) {
                    $navigationPage->setLabel('');
                    $navigationPage->addPage(array( 
                        'uri' => '',
                        'label' => 'Change password',
                        'active' => true
                    ));
                }
               
                // create password form
                if (empty($data['password'])) {           
                    $form = new Password2Form();
                } else {
                    $data['password'] = '';
                    $form = new PasswordForm();
                }        
                $form->setController($this)                    
                     ->create()
                     ->bindData($data);
               
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();                
                    $form->setData($post);     
                    if ($form->isValid()) {     
                        $post['_id'] = $id;
                        Api::call('url_users_updatepassword', $post); 
                        if (empty(Api::error())) {
                            Users::removeCache($id);
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
                $this->setHead(array(
                    'title' => $this->translate('Order list')
                ));
                $navigationPage = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_my_index');
                if ($navigationPage) {
                    $navigationPage->setLabel('');
                    $navigationPage->addPage(array( 
                        'uri' => '',
                        'label' => 'Order list',
                        'active' => true
                    ));
                }
                
                $param = $this->getParams(array(
                    'page' => 1,
                    'limit' => \Application\Module::getConfig('general.default_limit'),
                    'sort' => 'created-desc',            
                    'user_id' => $data['user_id']
                ));
                // create list form
                $orderList = Api::call('url_productorders_lists', $param);                
                if (!empty($orderList)) {
                    foreach ($orderList['data'] as &$order) {
                        $order['created'] = datetime_format($order['created']);                        
                        $order['total_money'] = app_money_format($order['total_money']);
                        switch ($order['status']) {
                            case 'done':
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status done\">" . $this->translate('Done') . "</span>" ;
                                break;
                            case 'shipping':
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status shipping\">" . $this->translate('Shipping') . "</span>" ;
                                break;
                            case 'cancel':
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status cancel\">" . $this->translate('Canceled') . "</span>" ;
                                break;
                            default:
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status new\">" . $this->translate('Processing') . "</span>" ;
                                break;
                        }
                        
                    }
                    unset($order);
                }
                $listForm = new ProductOrderListForm();
                $listForm   ->setController($this)
                            ->setAttribute('id', 'orderListForm')
                            ->setDataset($orderList)
                            ->create();
                break; 
            
            case 'allorders': 
                if (!$this->isAdmin()) {
                    exit;
                } 
                $this->setHead(array(
                    'title' => $this->translate('Manage orders')
                ));
                $navigationPage = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_my_index');
                if ($navigationPage) {
                    $navigationPage->setLabel('');
                    $navigationPage->addPage(array( 
                        'uri' => '',
                        'label' => 'Manage orders',
                        'active' => true
                    ));
                }
                
                $param = $this->getParams(array(
                    'page' => 1,
                    'limit' => \Application\Module::getConfig('general.default_limit'),
                    'sort' => 'order_id-desc'
                ));
                // create list form
                $orderList = Api::call('url_productorders_lists', $param);               
                if (!empty($orderList)) {
                    foreach ($orderList['data'] as &$order) {
                        $order['created'] = datetime_format($order['created']);                        
                        $order['total_money'] = app_money_format($order['total_money']);
                        switch ($order['status']) {
                            case 'done':
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status done\">" . $this->translate('Done') . "</span>" ;
                                break;
                            case 'shipping':
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status shipping\">" . $this->translate('Shipping') . "</span>" ;
                                break;
                            case 'cancel':
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status cancel\">" . $this->translate('Canceled') . "</span>" ;
                                break;
                            default:
                                $order['status_name'] = "<span style=\"display:inline\" class=\"btn-flat order-status new\">" . $this->translate('Processing') . "</span>" ;
                                break;
                        }
                    }
                    unset($order);
                }
                $listForm = new AllOrderListForm();
                $listForm   ->setController($this)
                            ->setAttribute('id', 'orderListForm')
                            ->setDataset($orderList)
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
    
    /**
     * Detail order
     *
     * @return Zend\View\Model
     */
    public function orderdetailAction()
    { 
        $request = $this->getRequest();        
        $id = $this->params()->fromRoute('id', 0);
        $locale = \Application\Module::getConfig('general.default_locale');
        
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }
        
        // get place detail             
        $data = Api::call(
            'url_productorders_detail', 
            array(
                '_id' => $id, 
                'locale' => $locale
            )
        );
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        
        return $this->getViewModel(array(
                'data' => $data
            )
        );  
    }
    
    public function submitcancelAction()
    { 
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if (!empty($id)           
            && $request->isXmlHttpRequest()) {
            $post = array(
                '_id' => $id,
                'is_cancel' => '1',
                'get_detail' => '1',
            );
            Api::call('url_productorders_update', $post);  
            if (empty(Api::error())) {
                $result['date'] = datetime_format();
                $result['status'] = 'OK';
            } else {
                $result['status'] = 'FAIL';
                $result['error'] = $this->getErrorMessage();
            }
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }

    public function submitshippingAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if (!empty($id)           
            && $request->isXmlHttpRequest()) {
            $post = array(
                '_id' => $id,
                'is_shipping' => '1',
                'get_detail' => '1',
                'send_email' => '1'
            );
            Api::call('url_productorders_update', $post);  
            if (empty(Api::error())) {
                $result['date'] = datetime_format();
                $result['status'] = 'OK';
            } else {
                $result['status'] = 'FAIL';
                $result['error'] = $this->getErrorMessage();
            }
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    public function submitpaymentAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if (!empty($id)           
            && $request->isXmlHttpRequest()) {
            $post = array(
                '_id' => $id,
                'is_paid' => '1',
                'get_detail' => '1',
                'send_email' => '1'
            );
            Api::call('url_productorders_update', $post);  
            if (empty(Api::error())) {
                $result['date'] = datetime_format();
                $result['status'] = 'OK';
            } else {
                $result['status'] = 'FAIL';
                $result['error'] = $this->getErrorMessage();
            }
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    public function submitdoneAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        } 
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if (!empty($id)           
            && $request->isXmlHttpRequest()) {
            $post = array(
                '_id' => $id,
                'is_done' => '1',
                'get_detail' => '1',
                'send_email' => '1'
            );
            Api::call('url_productorders_update', $post);  
            if (empty(Api::error())) {
                $result['date'] = datetime_format();
                $result['status'] = 'OK';
            } else {
                $result['status'] = 'FAIL';
                $result['error'] = $this->getErrorMessage();
            }
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
}
