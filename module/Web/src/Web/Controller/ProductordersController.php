<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Application\Lib\Api;
use Application\Lib\Cart;
use Application\Model\Images;
use Application\Model\LocaleStates;
use Application\Model\LocaleCities;
use Admin\Form\ProductOrder\SearchForm;
use Admin\Form\ProductOrder\ListForm;
use Admin\Form\ProductOrder\AddForm;
use Admin\Form\ProductOrder\UpdateForm;
use Admin\Form\ProductOrder\UpdateLocaleForm;
use Admin\Form\ProductOrder\AddProductForm;
use Admin\Form\ProductOrder\ProductListForm;
use Admin\Form\ProductOrder\CartProductListForm;

class ProductordersController extends AppController
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
        $request = $this->getRequest();
        
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit'),
            'sort' => 'created-desc',            
            'country_code' => \Application\Module::getConfig('general.default_country_code'),            
            //'state_code' => \Application\Module::getConfig('general.default_state_code'),            
        ));
        
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
        
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            
            // on/off websites.active
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_productorders_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
            
            // update productorders.sort
            if (!empty($post['sort'])) {  
                $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                Api::call('url_productorders_updatesort', $post); 
                if (empty(Api::error())) {
                    if ($request->isXmlHttpRequest()) {
                        echo 'OK';
                        exit;
                    }
                    $this->addSuccessMessage('Data saved successfully');   
                    return $this->redirect()->toUrl($request->getRequestUri());
                }                
            }
        }
        
       
        // create list form
        $listForm = new ListForm();
        $listForm   ->setController($this)
                    ->setDataset(Api::call('url_productorders_lists', $param))
                    ->create();
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
     /**
     * Add a Place
     *
     * @return Zend\View\Model
     */
    public function addAction()
    {
        $request = $this->getRequest();
        
        // create add/edit form
        $form = new AddForm();
        if ($request->isPost()) {
            $post = (array) $request->getPost();        
            if (!empty($post['user_id'])) {
                $user = Api::call('url_users_detail', array('user_id' => $post['user_id']));
                if (!empty($user)) {
                    $form->remove('user_id');   
                }
                $addressList = array();
                foreach ($user['addresses'] as $address) {
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
                    'user_id' => array(
                        'value_options' => 
                            array('' => '--Select one--') +
                            array(
                                $user['user_id'] => $user['name'] 
                            )
                    )
                )); 
                $form->setElementOptions(array(
                    'address_id' => array(
                        'value_options' => 
                            array('' => '--Select one--') +
                           $addressList
                    )
                )); 
            }
        }
        $form->setController($this)
             ->create();
        
        $cartItems = Cart::get(true);
        $totalQuantity = 0;
        $totalMoney = 0;
        if (!empty($cartItems)) {
            $detailForm = new CartProductListForm();
            $detailForm
                //->setAttribute('keyId', 'product_id')
                ->setAttribute('id', 'cartForm')
                ->setController($this)
                ->setDataset($cartItems)
                ->create();           
            foreach ($cartItems as $item) {
                $totalQuantity += $item['quantity'];
                $totalMoney += $item['quantity'] * $item['price'];
            }
        }
        
        // save form        
        if ($request->isPost()) {
            $post = (array) $request->getPost();            
            $form->setData($post);
            if ($form->isValid()) {                 
                if (!empty($cartItems)) {
                    if ($request->isXmlHttpRequest() && !empty($post['quantity']) && !empty($post['price'])) {
                        Cart::update($post);
                        $result['status'] = 'OK';
                        die(\Zend\Json\Encoder::encode($result));
                    }            
                    $post['products'] =  \Zend\Json\Encoder::encode($cartItems);
                }
                $id = Api::call('url_productorders_add', $post);
                if (Api::error()) {                   
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    Cart::reset();
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/productorders', 
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
                'totalMoney' => $totalMoney,               
                'detailForm' => !empty($detailForm) ? $detailForm : null,
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
       
        $locales = \Application\Module::getConfig('general.locales');        
        if (!isset($locales[$tab])) {
            $locale = \Application\Module::getConfig('general.default_locale');
        } else {
            $locale = $tab;
        }
        
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
        
        // create edit form
        $form = new UpdateForm(); 
        if (!empty($data['image_id'])) {
            $data['url_image'] = Images::getUrl($data['image_id'], 'productorders', true);
        }
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
        if (isset($data['cancel'])) {
            unset($data['cancel']);
        }
        $form->setController($this)
             ->create()
             ->bindData($data);

         // create add product form
        $addProductForm = new AddProductForm(); 
        $addProductForm->setController($this)
            ->setAttribute('id', 'productForm')
            ->create()
            ->bindData(array('quantity' => '1'));

        $products = !empty($data['products']) ? $data['products'] : array();
        if (!empty($products)) {
            $totalMoney = 0;
            foreach ($products as &$product) {                
                if (!empty($product['active'])) {
                    $totalMoney += $product['total_money'];                    
                }
                $product['total_money'] = money_format($product['total_money']); 
            }
        }
        $detailForm = new ProductListForm();
        $detailForm->setController($this)
            ->setAttribute('id', 'orderDetailForm')
            ->setDataset($products)
            ->create();

        // save general form
        if ($request->isPost()) {
            $post = (array) $request->getPost();      
            $post['order_id'] = $data['order_id'];                    
            if ($request->isXmlHttpRequest()) {                
                // reload order detail by ajax
                if (isset($post['loaddetail'])) {                    
                    return $this->getViewModel(array(
                            'detailForm' => $detailForm,
                        )
                    );                    
                }
                
                // on/off product_order_has_products.active 
                if (isset($post['_id']) && isset($post['value'])) { 
                    $post['get_order'] = 1;
                    $result = Api::call(
                        'url_productorders_onoffproduct', 
                        $post
                    );
                    if (!empty($result['total_money'])) {
                        $result['total_money'] = money_format($result['total_money']);
                    }
                    $result['status'] = 'OK';
                    die(\Zend\Json\Encoder::encode($result));
                }
                
                // add a product   
                if (isset($post['product_id']) && isset($post['quantity'])) { 
                    $addProductForm->setData($post);
                    if (!$addProductForm->isValid() && !empty($addProductForm->getMessages())) {
                        die($this->getErrorMessageForAjax($addProductForm->getMessages()));
                    } else {
                        $error = array(
                            array(
                                'field' => 'product_id', 
                                'code' => 1011, 
                                'message' => $this->translate('Product already exists')
                            ),                        
                        );
                        $post['get_order'] = 1;
                        $result = Api::call('url_productorders_addproduct', $post);
                        if (empty(Api::error())) {                                   
                            if (!empty($result['total_money'])) {
                                $result['total_money'] = money_format($result['total_money']);
                            }
                            $result['status'] = 'OK';
                            $result['message'] = 'Data saved successfully';
                            die(\Zend\Json\Encoder::encode($result));
                        }
                        die($this->getErrorMessageForAjax(array(), $error));                           
                    }
                }
            }    

            // save general info
            $form->setData($post);
            if ($form->isValid()) { 
                if (isset($post['save'])) { // save general info
                    Api::call('url_productorders_update', $post);  
                } elseif (isset($post['saveDetail'])) { // save detail
                    $post['quantity'] = \Zend\Json\Encoder::encode($post['quantity']);
                    $post['price'] = \Zend\Json\Encoder::encode($post['price']);
                    Api::call('url_productorders_savedetail', $post);                            
                }
                if (empty(Api::error())) {
                    $this->addSuccessMessage('Data saved successfully');
                    if (isset($post['saveAndBack']) && $backUrl) {
                        return $this->redirect()->toUrl(base64_decode($backUrl));
                    }
                    return $this->redirect()->toUrl($request->getRequestUri());
                }
            }                    
        }  
        
        if (Api::error()) {
            $this->addErrorMessage($this->getErrorMessage());
        }
        
        return $this->getViewModel(array(
                'form' => $form,
                'totalMoney' => !empty($totalMoney) ? $totalMoney : 0,
                'addProductForm' => !empty($addProductForm) ? $addProductForm : null,
                'detailForm' => !empty($detailForm) ? $detailForm : null,
            )
        );       
    }  
    
    /**
     * Ajax save order detail
     *
     * @return Zend\View\Model
     */
    public function savedetailAction()
    { 
        $request = $this->getRequest();
        $orderId = $this->params()->fromQuery('order_id', 0);
        if (!empty($orderId) && $request->isPost() && $request->isXmlHttpRequest()) {
            $post = (array) $request->getPost();
            if (!empty($post['quantity']) && !empty($post['price'])) {
                Api::call('url_productorders_savedetail', array(
                    'order_id' => $orderId,
                    'quantity' => \Zend\Json\Encoder::encode($post['quantity']),
                    'price' => \Zend\Json\Encoder::encode($post['price'])
                ));
                if (empty(Api::error())) {
                    $totalQuantity = 0;
                    $totalMoney = 0;
                    foreach ($post['quantity'] as $productId => $quantity) {
                        if (isset($post['price'][$productId])) {
                            $quantity = db_int($quantity);
                            $price = db_float($post['price'][$productId]);
                            $totalQuantity += $quantity;
                            $totalMoney += $quantity * $price;                        
                        }
                    }                    
                    $result['totalQuantity'] = $totalQuantity;
                    $result['totalMoney'] = money_format($totalMoney); 
                    $result['status'] = 'OK';
                } else {
                    $result['status'] = 'FAIL';
                    $result['error'] = $this->getErrorMessage();
                }
                die(\Zend\Json\Encoder::encode($result));
            }
        }
        exit;
    }
    
    /**
     * Ajax remove product
     *
     * @return Zend\View\Model
     */
    public function removeproductAction()
    { 
        $request = $this->getRequest();
        $orderId = $this->params()->fromQuery('order_id', 0);
        $productId = $this->params()->fromQuery('product_id', 0);
        if (!empty($orderId) 
            && !empty($productId) 
            && $request->isPost() 
            && $request->isXmlHttpRequest()) {
            $post = (array) $request->getPost();
            Api::call('url_productorders_onoffproduct', array(
                'order_id' => $orderId,
                '_id' => $productId,
                'value' => 0,
            ));
            if (empty(Api::error())) {
                $totalQuantity = 0;
                $totalMoney = 0;
                foreach ($post['quantity'] as $productId => $quantity) {
                    if (isset($post['price'][$productId])) {
                        $quantity = db_int($quantity);
                        $price = db_float($post['price'][$productId]);
                        $totalQuantity += $quantity;
                        $totalMoney += $quantity * $price;                        
                    }
                }                    
                $result['totalQuantity'] = $totalQuantity;
                $result['totalMoney'] = money_format($totalMoney); 
                $result['status'] = 'OK';
            } else {
                $result['status'] = 'FAIL';
                $result['error'] = $this->getErrorMessage();
            }
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Detail order
     *
     * @return Zend\View\Model
     */
    public function detailAction()
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
        ); //p($data, 1);
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        
        return $this->getViewModel(array(
                'data' => $data
            )
        );  
    }
    
    /**
     * Ajax remove product
     *
     * @return Zend\View\Model
     */
    public function submitshippingAction()
    { 
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if (!empty($id)           
            && $request->isXmlHttpRequest()) {
            $post = array(
                '_id' => $id,
                'is_shipping' => '1',
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
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if (!empty($id)           
            && $request->isXmlHttpRequest()) {
            $post = array(
                '_id' => $id,
                'is_paid' => '1',
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
    
    public function submitcancelAction()
    { 
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if (!empty($id)           
            && $request->isXmlHttpRequest()) {
            $post = array(
                '_id' => $id,
                'is_cancel' => '1',
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
