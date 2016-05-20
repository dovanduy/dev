<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Web\Lib\Cart;

class CartsController extends AppController
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
     * Ajax add a product to cart
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    { 
        $request = $this->getRequest();        
        if ($request->isXmlHttpRequest()) {
            return $this->getViewModel();          
        }
        exit;
    }
    
     /**
     * 
     *
     * @return Zend\View\Model
     */
    public function viewAction()
    { 
        $request = $this->getRequest(); 
        return $this->getViewModel(array(
                             
            )
        );
    }
    
    /**
     * Ajax add a product to cart
     *
     * @return Zend\View\Model
     */
    public function additemAction()
    { 
        $request = $this->getRequest();    
        $id = $this->params()->fromRoute('id', 0);
        if ($request->isXmlHttpRequest() && !empty($id)) {
            $post = $request->getPost();
            $sizeId = !empty($post['size_id']) ? $post['size_id'] : 0;
            $colorId = !empty($post['color_id']) ? $post['color_id'] : 0;
            $quantity = !empty($post['quantity']) ? $post['quantity'] : 1;
            Cart::addProduct($id, $quantity, $sizeId, $colorId);
            $result['status'] = 'OK';            
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
     /**
     * Ajax remove a product to cart
     *
     * @return Zend\View\Model
     */
    public function removeitemAction()
    {
        $request = $this->getRequest();        
        $keyId = $this->params()->fromRoute('id', 0);
        if ($request->isXmlHttpRequest() && !empty($keyId)) {
            $cartItems = Cart::removeProduct($keyId);
            $totalQuantity = 0;
            $totalMoney = 0;
            foreach ($cartItems as &$item) {
                $totalQuantity += db_int($item['quantity']);
                $totalMoney += db_int($item['quantity']) * db_float($item['price']);
                $item['price'] = app_money_format($item['price']);
                $item['total_money'] = app_money_format($item['total_money']);
            }
            $result['items'] = $cartItems;
            $result['totalQuantity'] = $totalQuantity;
            $result['totalMoney'] = app_money_format($totalMoney);
            $result['status'] = 'OK';
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Ajax update items in cart
     *
     * @return Zend\View\Model
     */
    public function updateitemsAction()
    { 
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();
            $cartItems = Cart::get();
            if (!empty($cartItems) && !empty($post)) {
                if ($request->isXmlHttpRequest() && !empty($post['quantity'])) {
                    $cartItems = Cart::update($post);
                    $totalQuantity = 0;
                    $totalMoney = 0;
                    foreach ($cartItems as &$item) {
                        $totalQuantity += db_int($item['quantity']);
                        $totalMoney += db_int($item['quantity']) * db_float($item['price']);
                        $item['price'] = app_money_format($item['price']);
                        $item['total_money'] = app_money_format($item['total_money']);
                    }                    
                    $result['items'] = $cartItems;
                    $result['totalQuantity'] = $totalQuantity;
                    $result['totalMoney'] = app_money_format($totalMoney);
                    $result['status'] = 'OK';
                    die(\Zend\Json\Encoder::encode($result));
                }
            }
        }
        exit;
    }
    
}
