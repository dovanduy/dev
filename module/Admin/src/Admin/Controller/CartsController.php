<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Application\Lib\Cart;

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
     * Ajax add a product to cart
     *
     * @return Zend\View\Model
     */
    public function additemAction()
    { 
        $request = $this->getRequest();        
        $id = $this->params()->fromRoute('id', 0);
        if ($request->isXmlHttpRequest() && !empty($id)) {
            Cart::addProduct($id);
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
        $id = $this->params()->fromRoute('id', 0);
        if ($request->isXmlHttpRequest() && !empty($id)) {
            $cartItems = Cart::removeProduct($id);
            //$cartItems = Cart::get();
            $totalQuantity = 0;
            $totalMoney = 0;
            foreach ($cartItems as &$item) {
                $totalQuantity += $item['quantity'];
                $totalMoney += $item['quantity'] * $item['price'];
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
                if ($request->isXmlHttpRequest() && !empty($post['quantity']) && !empty($post['price'])) {
                    $cartItems = Cart::update($post);
                    $totalQuantity = 0;
                    $totalMoney = 0;
                    foreach ($cartItems as &$item) {
                        $totalQuantity += $item['quantity'];
                        $totalMoney += $item['quantity'] * $item['price'];
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
