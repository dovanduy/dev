<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Application\Lib\Cart;
use Application\Lib\Session;
use Application\Lib\Arr;
use Application\Model\LocaleStates;
use Application\Model\LocaleCities;
use Web\Lib\Api;
use Web\Form\Checkout\RegisterForm;
use Web\Form\Checkout\RegisterAddressForm;
use Web\Form\Checkout\ReviewForm;

class CheckoutController extends AppController
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
        $auth = $this->getServiceLocator()->get('auth');
        if ($auth->hasIdentity()) {
            $AppUI = $auth->getIdentity();
            $user = Api::call(
                'url_users_detail', 
                array(
                    '_id' => $AppUI->_id, 
                )
            );
            // not found data
            if (empty($user)) {
                return $this->notFoundAction();
            } 
            
            $registerAddressForm = new RegisterAddressForm();
            $addresses = array();
            if (!empty($user['addresses'])) {        
                $addresses = Arr::filter($user['addresses'], 'active', 1);
                $addresses = Arr::keyValue($addresses, 'address_id', 'address_full');                               
            }
            if (!empty($addresses)) {
                $registerAddressForm->setElementOptions(array(
                    'address_id' => array(
                        'value_options' => $addresses + array('' => $this->translate('Other delivery address'))
                    )
                ));                    
            } else {
                $registerAddressForm->setElementOptions(array(
                    'address_id' => array(
                        'value_options' => array('' => $this->translate('Other delivery address')),                            
                    )
                ));
            }
            
            $checkoutInfo = Session::get('checkout_step1');
            if ($request->isPost()) {
                $post = (array) $request->getPost();
                $checkoutInfo = array_replace_recursive($checkoutInfo, $post); 
            }
            if (empty($checkoutInfo['email'])) { 
                $checkoutInfo['email'] = $AppUI->email;
            }
            if (empty($checkoutInfo['name'])) { 
                $checkoutInfo['name'] = $AppUI->name;
            }
            if (empty($checkoutInfo['mobile'])) { 
                $checkoutInfo['mobile'] = $AppUI->mobile;
            }
            if (empty($checkoutInfo['country_code'])) { 
                $checkoutInfo['country_code'] = \Application\Module::getConfig('general.default_country_code');
            }
            if (empty($checkoutInfo['state_code'])) { 
                $checkoutInfo['state_code'] = \Application\Module::getConfig('general.default_state_code');
            }
            if (!empty($checkoutInfo['country_code'])) {                   
                $registerAddressForm->setElementOptions(array(
                    'state_code' => array(
                        'value_options' => LocaleStates::getAll($checkoutInfo['country_code'])
                    )
                ));
                if (!empty($checkoutInfo['state_code'])) {                    
                    $registerAddressForm->setElementOptions(array(
                        'city_code' => array(
                            'value_options' => LocaleCities::getAll($checkoutInfo['state_code'], $checkoutInfo['country_code'])
                        )
                    ));
                }                    
            } 
            $registerAddressForm->setController($this)
                ->setAttribute('id', 'registerForm')
                ->setAttribute('class', 'form-horizontal')
                ->create()
                ->bindData($checkoutInfo);

            if ($request->isPost()) {
                $post = (array) $request->getPost();
                if (!empty($post['address_id'])) {
                    $foundAddress = false;
                    foreach ($user['addresses'] as $address) { 
                        if ($address['address_id'] == $post['address_id']) {                         
                            if (!empty($address['street'])) {
                                $post['street'] = $address['street']; 
                            }
                            if (!empty($address['city_code'])) {
                                $post['city_code'] = $address['city_code']; 
                            }
                            if (!empty($address['state_code'])) {
                                $post['state_code'] = $address['state_code']; 
                            }
                            if (!empty($address['country_code'])) {
                                $post['country_code'] = $address['country_code']; 
                            }                           
                            if (!empty($address['name'])) {
                                $post['address_name'] = $address['name']; 
                            }
                            $foundAddress = true;
                            break;
                        }
                    }
                    if ($foundAddress == false) {
                        return $this->notFoundAction();
                    }
                    $registerAddressForm->setElementOptions(array(
                        'country_code' => array(
                            'value' => $post['country_code']
                        )
                    ));
                    $registerAddressForm->setElementOptions(array(
                        'state_code' => array(
                            'value' => $post['state_code']
                        )
                    ));
                    $registerAddressForm->setElementOptions(array(
                        'city_code' => array(
                            'value' => $post['city_code']
                        )
                    ));
                    $registerAddressForm->setElementOptions(array(
                        'name' => array(
                            'value' => $post['address_name']
                        )
                    ));
                    $registerAddressForm->setElementOptions(array(
                        'name' => array(
                            'street' => $post['street']
                        )
                    ));
                }
                $registerAddressForm->setData($post);
                if ($registerAddressForm->isValid()) {
                    Session::set('checkout_step1', array(
                        'email' => $post['email'],
                        'name' => $post['name'],
                        'mobile' => $post['mobile'],                            
                        'address_id' => $post['address_id'],
                        'country_code' => $post['country_code'],
                        'state_code' => $post['state_code'],
                        'city_code' => $post['city_code'],
                        'street' => $post['street'],
                        'address_name' => $post['address_name'], 
                    ));                       
                    return $this->redirect()->toRoute(
                        'web/checkout',
                        array('action' => 'review')
                    );                        
                } else {
                    $checkoutInfo['address_id'] = $post['address_id'];
                    Session::set('checkout_step1', $checkoutInfo);
                }
            } 
            
        } else {            
            
            $registerForm = new RegisterForm();     
            $address['country_code'] = \Application\Module::getConfig('general.default_country_code');
            $address['state_code'] = \Application\Module::getConfig('general.default_state_code');
            if ($request->isPost()) {
                if (!empty($request->getPost('country_code'))) {
                    $address['country_code'] = $request->getPost('country_code');
                    if (!empty($request->getPost('state_code'))) {
                        $address['state_code'] = $request->getPost('state_code');
                    }
                }
            }
            // set data for dropdown state_code
            $registerForm->setElementOptions(array(
                'state_code' => array(
                    'value_options' => LocaleStates::getAll($address['country_code'])
                )
            ));
            if (!empty($address['state_code'])) {
                // set data for dropdown city_code
                $registerForm->setElementOptions(array(
                    'city_code' => array(
                        'value_options' => LocaleCities::getAll($address['state_code'], $address['country_code'])
                    )
                ));
            }            
            $registerForm->setController($this)
                ->setAttribute('id', 'registerForm')
                ->setAttribute('class', 'form-horizontal')
                ->create()
                ->bindData($address);
            
            if ($request->isPost()) {
                $post = (array) $request->getPost();     
                $registerForm->setData($post);   
                if ($registerForm->isValid()) {
                    $id = Api::call('url_users_register', $post);
                    if (Api::error()) {
                        $this->addErrorMessage($this->getErrorMessage(array(), Api::error()));
                    } else {
                        $auth = $this->getServiceLocator()->get('auth');
                        if ($auth->authenticate($post['email'], $post['password'], 'web')) {
                            $AppUI = $auth->getIdentity();
                            Session::set('checkout_step1', array(
                                'email' => $post['email'],
                                'name' => $post['name'],
                                'mobile' => $post['mobile'],
                                'country_code' => $post['country_code'],
                                'state_code' => $post['state_code'],
                                'city_code' => $post['city_code'],
                                'street' => $post['street'],
                                'address_id' => $AppUI->address_id,
                                'address_name' => $post['address_name'], 
                            ));
                            $this->addSuccessMessage('Account registed successfully');
                            return $this->redirect()->toRoute(
                                'web/checkout',
                                array('action' => 'review')
                            );
                        }                        
                    }
                }
            }
        }
                
        return $this->getViewModel(array(                
               'registerForm' => isset($registerForm) ? $registerForm : null,              
               'registerAddressForm' => isset($registerAddressForm) ? $registerAddressForm : null,              
               'user' => isset($user) ? $user : null,              
            )
        );
    }
    
    /**
     * Ajax remove a address
     *
     * @return Zend\View\Model
     */
    public function removeaddressAction()
    {
        $request = $this->getRequest();        
        $id = $this->params()->fromRoute('id', 0);
        if ($request->isXmlHttpRequest() && !empty($id)) {
            Api::call(
                'url_addresses_onoff', 
                array(
                    '_id' => $id,
                    'value' => '0',
                )
            );
            $result['status'] = 'OK';
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Ajax remove a address
     *
     * @return Zend\View\Model
     */
    public function chooseaddressAction()
    {
        $request = $this->getRequest();        
        $id = $this->params()->fromRoute('id', 0);
        if ($request->isXmlHttpRequest() && !empty($id)) {
            $auth = $this->getServiceLocator()->get('auth');
            if ($auth->hasIdentity()) {
                $AppUI = $auth->getIdentity();
                $user = Api::call(
                    'url_users_detail', 
                    array(
                        '_id' => $AppUI->_id, 
                    )
                );
                foreach ($user['addresses'] as $address) {
                    if ($address['active'] == 1 && $address['_id'] == $id) {
                        $AppUI->address_id = $address['address_id'];
                        $AppUI->address = $address;
                        $auth->getStorage()->write($AppUI);
                    }
                }   
            }
            $result['status'] = 'OK';
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
     /**
     * Ajax order
     *
     * @return Zend\View\Model
     */
    public function orderAction()
    {
        $request = $this->getRequest();    
        $reviewForm = new ReviewForm();
        $reviewForm->setController($this)
                ->setAttribute('id', 'reviewForm')
                ->setAttribute('class', 'form-horizontal')
                ->create();
        
        $id = $this->params()->fromRoute('id', 0);
        $AppUI = $this->getLoginInfo();
        $checkoutInfo = Session::get('checkout_step1');
        if ($request->isXmlHttpRequest() && !empty($AppUI)) {            
            $post = (array) $request->getPost();  
            $reviewForm->setData($post);
            if (!$reviewForm->isValid() || !isset($post['confirmation'])) {
                exit;
            }                 
            $post['user_id'] = $AppUI->user_id;            
            $post['user_name'] = !empty($checkoutInfo['name']) ? $checkoutInfo['name'] : $AppUI->name;
            $post['user_email'] = !empty($checkoutInfo['email']) ? $checkoutInfo['email'] : $AppUI->email;
            $post['user_phone'] = !empty($checkoutInfo['phone']) ? $checkoutInfo['phone'] : $AppUI->phone;
            $post['user_mobile'] = !empty($checkoutInfo['mobile']) ? $checkoutInfo['mobile'] : $AppUI->mobile;
            if (!empty($checkoutInfo['address_id'])) {
                $post['user_address_id'] = $checkoutInfo['address_id'];
            } else {
                $post['user_address_id'] = 0; 
            }
            if (!empty($checkoutInfo['address_name'])) {
                $post['user_address_name'] = $checkoutInfo['address_name'];
            }
            if (!empty($checkoutInfo['country_code'])) {
                $post['user_country_code'] = $checkoutInfo['country_code'];
            }
            if (!empty($checkoutInfo['state_code'])) {
                $post['user_state_code'] = $checkoutInfo['state_code'];
            }
            if (!empty($checkoutInfo['city_code'])) {
                $post['user_city_code'] = $checkoutInfo['city_code'];
            }
            if (!empty($checkoutInfo['street'])) {
                $post['user_street'] = $checkoutInfo['street'];
            }            
            if (!empty($checkoutInfo['city_code']) 
                && !empty($checkoutInfo['state_code']) 
                && !empty($checkoutInfo['country_code'])) {
                $cities = \Application\Model\LocaleCities::getAll($checkoutInfo['state_code'], $checkoutInfo['country_code']);                                                
                $post['user_city_name'] = $cities[$checkoutInfo['city_code']];
            }
            if (!empty($checkoutInfo['state_code']) 
                && !empty($checkoutInfo['country_code'])) {
                $states = \Application\Model\LocaleStates::getAll($checkoutInfo['country_code']);                                                
                $post['user_state_name'] = $states[$checkoutInfo['state_code']];
            }                                   
            if (!empty($checkoutInfo['country_code'])) {
                $countries = \Application\Model\LocaleCountries::getAll();  
                $post['user_country_name'] = $countries[$checkoutInfo['country_code']];
            }
            $cartItems = Cart::get(true);
            $totalQuantity = 0;
            $totalMoney = 0;
            if (!empty($cartItems)) {                         
                foreach ($cartItems as $item) {
                    $totalQuantity += $item['quantity'];
                    $totalMoney += $item['quantity'] * $item['price'];
                }
            }
            $post['total_money'] = $totalMoney;
            $post['products'] =  \Zend\Json\Encoder::encode($cartItems);  
            $post['send_email'] = 1;
            $_id = Api::call('url_productorders_add', $post);
            if (empty(Api::error())) {                          
                Session::remove('checkout_step1');
                Session::set('checkout_order_id', $_id);
                Cart::reset();
                $result['status'] = 'OK';
                $result['message'] = 'Data saved successfully';
                $result['id'] = $_id;
                die(json_encode($result));
            }
            die($this->getErrorMessageForAjax()); 
        }
        exit;
    }
    
    /**
     * 
     *
     * 
     *
     * @return Zend\View\Model
     */
    public function paymentAction()
    { 
        $request = $this->getRequest(); 
        return $this->getViewModel(array(
                             
            )
        );
    }
    
    /**
     * 
     *
     * @return Zend\View\Model
     */
    public function reviewAction()
    {     
        $request = $this->getRequest(); 
        $reviewForm = new ReviewForm();
        $reviewForm->setController($this)
                ->setAttribute('id', 'reviewForm')
                ->setAttribute('class', 'form-horizontal')
                ->create();
        
        return $this->getViewModel(array(
                'reviewForm' => $reviewForm           
            )
        );
    }
    
    /**
     * 
     *
     * @return Zend\View\Model
     */
    public function completedAction()
    { 
        $request = $this->getRequest();       
        $_id = Session::get('checkout_order_id');
        empty($_id) or $order = Api::call('url_productorders_detail', array('_id' => $_id));
        if (empty($order)) {
            return $this->notFoundAction();
        }
        $navigationPage = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_checkout_index');
        if (!empty($navigationPage)) { 
            $navigationPage->setLabel('');
            $navigationPage->addPage(array( 
                'uri' => '',
                'label' => $this->translate('Thank you'),
                'active' => true
            ));
        }
        return $this->getViewModel(array(
                'data' => $order        
            )
        );
    }
    
}
