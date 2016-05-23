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
use Web\Model\LocaleCities;
use Web\Model\LocaleStates;
use Web\Lib\Api;

class AjaxController extends AppController
{
    public function indexAction()
    {   
        return $this->getViewModel();
    }
    
    public function localestateAction()
    {
        $param = $this->getParams();
        $result = array();
        if (!empty($param['country_code'])) {
            $result = LocaleStates::getAll($param['country_code'], false);
            $options = array();
            $options[] = "<option value=\"\">--Select one--</option>";
            foreach ($result as $row) {
                $options[] = "<option value=\"{$row['iso']}\">{$row['name']}</option>";
            }
            echo implode('', $options);            
        }        
        exit;
    }
    
    public function localecitiesAction()
    {
        $param = $this->getParams();
        $result = array();
        if (!empty($param['state_code'])) {
            $result = LocaleCities::getAll($param['state_code'], $param['country_code'], false);
            $options = array();
            $options[] = "<option value=\"\">--Select one--</option>";
            foreach ($result as $row) {
                $options[] = "<option value=\"{$row['code']}\">{$row['name']}</option>";
            }
            echo implode('', $options);              
        }
        exit;
    }
    
    public function localeaddressAction()
    {
        $param = $this->getParams();
        if (!empty($param['user_id'])) {
            $result = Api::call('url_addresses_all', array(
                'user_id' => $param['user_id']
            ));
            $addressList = array();
            foreach ($result as $address) {
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
            $options = array();
            $options[] = "<option value=\"\">--Select one--</option>";
            foreach ($addressList as $addressId => $name) {
                $options[] = "<option value=\"{$addressId}\">{$name}</option>";
            }
            echo implode('', $options);              
        }
        exit;
    }
    
    public function toggleAction()
    {
        $param = $this->getParams();
        if (!empty($param['url']) 
            && !empty($param['id']) 
            && !empty($param['field']) 
            && isset($param['value'])) {
            $parseUrl = parse_url($param['url']);
            preg_match("/\/([a-zA-Z]+)?+/", $parseUrl['path'], $match);
            $apiUrl = '';
            if (count($match) >= 2) {
                switch ($match[1]) {
                    case 'newscategories':
                        $apiUrl = 'url_news_categories_onoff';
                        break;
                    case 'news':
                        $apiUrl = 'url_news_onoff';
                        break;
                    case 'websitecategories':
                        $apiUrl = 'url_website_categories_onoff';
                        break;
                    case 'websites':
                        $apiUrl = 'url_websites_onoff';
                        break;
                    case 'admins':
                        $apiUrl = 'url_admins_onoff';
                        break;
                    case 'inputfields':
                        $apiUrl = 'url_inputfields_onoff';
                        break;
                    case 'inputoptions':
                        $apiUrl = 'url_inputoptions_onoff';
                        break;
                }
            }
            if ($apiUrl) {
                Api::call(
                    $apiUrl, 
                    $param
                );
            }
        }      
        exit;
    }
    
    public function searchuserAction()
    {
        $param = $this->getParams();
        if (isset($param['q'])) {
            $param['keyword'] = $param['q'];
        }
        $userList = Api::call('url_users_search', $param);      
        echo json_encode($userList);
        exit;
    }
    
    /**
     * Ajax add a product to block
     *
     * @return Zend\View\Model
     */
    public function addproducttoblockAction()
    {        
        if (!$this->isAdmin()) {
            exit;
        }
        $request = $this->getRequest();    
        $param = $this->getParams(); // p($param);
        if ($request->isXmlHttpRequest() && !empty($param['product_id'])) {
            $post = $request->getPost(); 
            $post['product_id'] = $param['product_id'];
            $post['block_id'] = $post['add_block_id'];
            $result = Api::call('url_blocks_addproduct', $post);                 
            if (empty(Api::error())) {   
                \Web\Model\Products::removeCache();
                $result = array(
                    'status' => 'OK',
                    'message' => 'Data saved successfully',
                );
                die(\Zend\Json\Encoder::encode($result));
            }              
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Ajax remove a product from block
     *
     * @return Zend\View\Model
     */
    public function removeproductfromblockAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() && !empty($param['product_id'])) {
            $post = $request->getPost(); 
            if (!empty($post['remove_block_id'])) {
                $param['block_id'] = $post['remove_block_id'];
            }
            if (empty($param['block_id'])) {
                exit;
            }
            $result = Api::call('url_blocks_removeproduct', $param);                 
            if (empty(Api::error())) {  
                \Web\Model\Products::removeCache();
                $result = array(
                    'status' => 'OK',
                    'message' => 'Data saved successfully',
                );
                die(\Zend\Json\Encoder::encode($result));
            }              
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Ajax add a product to category
     *
     * @return Zend\View\Model
     */
    public function addproducttocategoryAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() && !empty($param['product_id'])) {
            $post = $request->getPost(); 
            $post['product_id'] = $param['product_id'];           
            $result = Api::call('url_productcategories_addproduct', $post);                 
            if (empty(Api::error())) {                   
                $result = array(
                    'status' => 'OK',
                    'message' => 'Data saved successfully',
                );
                die(\Zend\Json\Encoder::encode($result));
            }              
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
     /**
     * Ajax remove a product from category
     *
     * @return Zend\View\Model
     */
    public function removeproductfromcategoryAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost() 
            && !empty($param['product_id']) 
            && !empty($param['category_id'])) {            
            $result = Api::call('url_productcategories_removeproduct', $param);                 
            if (empty(Api::error())) {                   
                $result = array(
                    'status' => 'OK',
                    'message' => 'Data saved successfully',
                );
                die(\Zend\Json\Encoder::encode($result));
            }             
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Ajax set display priority for product
     *
     * @return Zend\View\Model
     */
    public function setpriorityproductAction()
    {   
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost() 
            && !empty($param['product_id'])) {            
            $result = Api::call('url_products_setpriority', $param);                 
            if (empty(Api::error())) {                   
                $result = array(
                    'status' => 'OK',
                    'message' => 'Data saved successfully',
                );
                die(\Zend\Json\Encoder::encode($result));
            }             
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
}
