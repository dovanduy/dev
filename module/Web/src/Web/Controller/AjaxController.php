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
use Application\Lib\Log;
use Web\Model\LocaleCities;
use Web\Model\LocaleStates;
use Web\Model\Products;
use Web\Lib\Api;
use Web\Lib\Fb;
use Web\Module as WebModule;

class AjaxController extends AppController
{
    public function indexAction()
    {   
        return $this->getViewModel();
    }
    
    public function priceAction()
    {
        $param = $this->getParams();
        $result = array();
        if (!empty($param['product_id'])
            && isset($param['color_id'])
            && isset($param['size_id'])) {
            $result = Products::getPrice($param);
            if (!empty($result)) {
                die(\Zend\Json\Encoder::encode(array(
                    'price' => app_money_format($result['price'], -3),
                    'original_price' => app_money_format($result['original_price']),
                )));
            }
        }        
        exit;
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
        if (!$this->isAdmin()) {
            exit;
        }
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
    
    /**
     * Ajax share to facecbook
     *
     * @return Zend\View\Model
     */
    public function fbshareAction()
    {   
        if (!$this->isAdmin()) {
            exit;
        }        
        $AppUI = $this->getLoginInfo(); 
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost()
            && !empty($param['url'])
            && !empty($param['product_id'])
            && !empty($AppUI->facebook_id)
            && !empty($AppUI->fb_access_token)) {
            $product = Products::getDetail($param['product_id']);
            if (empty($product)) {
                exit;
            }
            $waitingFile = implode(DS, [WebModule::getConfig('facebook_album_dir'), 'waiting.txt']);
            if (!file_exists($waitingFile)) {
                $data = array();
            } else {
                $content = file_get_contents($waitingFile);
                if (empty($content)) {                   
                    $data = array();
                } else {
                    $data = json_decode($content, true);
                }
            }
            if (!empty($data)) {
                foreach ($data as $row) {
                    foreach ($row['product'] as $p) {
                        if ($product['product_id'] == $p['product_id']) {
                            $result = array(
                                'status' => 'OK',
                                'message' => 'Already existed',
                            );
                            die(\Zend\Json\Encoder::encode($result));	
                        }
                    }
                }
            }
            $product['url'] = $param['url']  . '?utm_source=facebook&utm_medium=social&utm_campaign=product';
            $product['short_url'] = Util::googleShortUrl($product['url']);
            if (isset($product['more'])) {
                unset($product['more']);
            }
            if (isset($product['content'])) {
                unset($product['content']);
            }
            if (isset($product['product_related'])) {
                unset($product['product_related']);
            }
            if (isset($product['product_reviews'])) {
                unset($product['product_reviews']);
            }
            $data[] = [
                'userId' => $AppUI->facebook_id,
                'accessToken' => $AppUI->fb_access_token,
                'product' => $product,
            ];                           
            file_put_contents($waitingFile, json_encode($data));
            $result = array(
                'status' => 'OK',
                'message' => 'Done',
            );
            die(\Zend\Json\Encoder::encode($result));          
            exit;
    
            if (empty($product['image_facebook'])) {
                $product['image_facebook'] = Util::uploadImageFromUrl($product['url_image'], 300, 300);
                if (!empty($product['image_facebook'])) {
                    $param['image_facebook'] = $product['image_facebook'];
                }                
            }  
            $dir = implode(DS, [WebModule::getConfig('facebook_album_dir'), $AppUI->facebook_id]);
            if (mk_dir($dir) === false) {            
                exit;
            }
            $file = implode(DS, [$dir, $param['product_id'] . '.txt']);
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $album = explode(PHP_EOL, utf8_encode($content)); 
                $albumId = $album[0];
                $commentList = app_get_comment_list();
                $commentIcon = array_rand($commentList);    
                $commentMessage = $commentList[$commentIcon];
                $commentData = [
                    'message' => $commentMessage,
                    'attachment_url' => $commentIcon
                ];
                $commentId = Fb::commentToPost($albumId, $commentData, $AppUI->fb_access_token);
                if (!empty($commentId)) {
                    die(\Zend\Json\Encoder::encode($commentId));
                }
                exit;
            }
            $param['url'] = str_replace('.dev', '.com', $param['url']);     
            $param['url'] = $param['url']  . '?utm_source=facebook&utm_medium=social&utm_campaign=product';
            $product['short_url'] = Util::googleShortUrl($param['url']);
            $price = app_money_format($product['price']);            
            if (!empty($product['discount_percent'])) {
                $price .= ' (đang giảm giá: ' . $product['discount_percent'] . '%)';
            }
            $short = mb_ereg_replace('!\s+!', ' ', $product['short']); 
            $albumData = [
                'name' => $product['name'],
                'message' => implode(PHP_EOL, [
                    "Giá: {$price}",
                    "Mã hàng: {$product['code']}",
                    "Nhắn tin đặt hàng: {$product['code']} gửi 098 65 60 997",
                    "Điện thoại đặt hàng: 097 443 60 40 - 098 65 60 997",   
                    "{$short}",
                    "Chi tiết {$product['short_url']}",
                    "Giao hàng TOÀN QUỐC. Free ship ở khu vực nội thành TP HCM (các quận 1, 2, 3, 4 ,5 ,6 ,7 ,8 ,10, 11, Bình Thạnh, Gò Vấp, Phú Nhuận, Tân Bình, Tân Phú)",
                ]),
            ];
            $albumId = Fb::meCreateAlbum($albumData, $AppUI->fb_access_token, $errorMessage);
            if (empty($albumId)) {
                $result = array(
                    'status' => 'OK',
                    'message' => $errorMessage,
                );
                die(\Zend\Json\Encoder::encode($result));		
            }
            $result[] = $albumId;
            $photos = array();
            if (!empty($product['colors']) && count($product['colors']) > 1) {
                foreach ($product['colors'] as $color) {   
                    $photos[$color['url_image']] = $product['name'] . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name']));
                }
            } else {
                $photos[$product['url_image']] = $product['url_image'];
            }                
            if (!empty($product['images'])) {
                foreach ($product['images'] as $image) {   
                    if (!isset($photos[$image['url_image']])) {
                        $photos[$image['url_image']] = $product['name'];
                    }
                }
            }
            foreach ($photos as $imageUrl => $name) {            
                $data = [       
                    'message' => implode(PHP_EOL, [
                        $name,
                        "Giá: {$price}",
                        $short,         
                        "Chi tiết {$product['short_url']}",
                    ]),
                    'url' => $imageUrl,
                    'no_story' => true
                ];
                $photoId = Fb::addPhotoToAlbum($albumId, $data, $AppUI->fb_access_token, $errorMessage); 
                if (!empty($photoId)) {
                    $result[] = $photoId;
                } else {
                    $result = array(
                        'status' => 'OK',
                        'message' => $errorMessage,
                    );
                    die(\Zend\Json\Encoder::encode($result));						
                }
            }            
            if (!empty($result)) {                
                file_put_contents($file, implode(PHP_EOL, $result));                
            }
			$result = array(
				'status' => 'OK',
				'message' => implode('<br/>', $result),
			);			
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Ajax share to facecbook
     *
     * @return Zend\View\Model
     */
    public function fbshare1Action()
    {   
        if (!$this->isAdmin()) {
            exit;
        }        
        $AppUI = $this->getLoginInfo(); 
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost()
            && !empty($param['url'])
            && !empty($param['product_id'])
            && !empty($AppUI->facebook_id)
            && !empty($AppUI->fb_access_token)) {
            $product = Products::getDetail($param['product_id']);
            if (empty($product)) {
                exit;
            }            
            if (empty($product['image_facebook'])) {
                $product['image_facebook'] = Util::uploadImageFromUrl($product['url_image'], 300, 300);
                if (!empty($product['image_facebook'])) {
                    $param['image_facebook'] = $product['image_facebook'];
                }                
            }            
            $param['url'] = str_replace('.dev', '.com', $param['url']);
            
            $param['data'] = serialize($product);
            $result = Api::call('url_shareurls_add', $param);
            if (empty(Api::error())) {    
                if (!empty($param['image_facebook'])) {
                    Products::removeCache($param['product_id']);
                }
                $product['price'] = app_money_format($product['price']);
                $fb = new \Facebook\Facebook([
                    'app_id' => WebModule::getConfig('facebook_app_id'),
                    'app_secret' => WebModule::getConfig('facebook_app_secret'),
                    'default_graph_version' => 'v2.6',
                    'default_access_token' => $AppUI->fb_access_token, // optional
                ]);  
                try {   
                    
                    $tags = array();
                    $tagIds = WebModule::getConfig('facebook_tag_ids');                
                    foreach ($tagIds as $friendId) {
                        if ($AppUI->facebook_id != $friendId) {
                            $tags[] = $friendId;
                        }
                    }
                    
                    $param['url'] = $param['url']  . '?utm_source=facebook&utm_medium=social&utm_campaign=product';
                    $product['short_url'] = Util::googleShortUrl($param['url']);                
                    $product['url'] = $param['url'];                
                    $product['tags'] = $tags;                
                    $data = app_get_fb_share_content($product);   
                    
                    $result = array(); 
                    
                    // post to wall 
                    $response = $fb->post('/me/feed', $data);
                    $graphNode = $response->getGraphNode();
                    if (!empty($graphNode['id'])) {
                        $result[] = $AppUI->facebook_id . ': ' . $graphNode['id'];
                    }
                    
                    // post to group 
                    if (1==0) {                    
                        unset($data['tags']);
                        $groupIds = WebModule::getConfig('facebook_group_ids');
                        $groupIds = array(
//                            '952553334783243', // Chợ online Khang Điền Q.9 https://www.facebook.com/groups/928701673904347/
//                            '928701673904347', // Chợ sinh viên giá rẻ https://www.facebook.com/groups/928701673904347/
//                            '1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
//                            '297906577042130', // Hội những người mê kinh doanh online
//                            '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
                            '209799659176359', // Rao vặt linh tinh
//                            '1482043962099325', // CHỢ RAO VẶT SÀI GÒN
//                            '312968818826910', // CHỢ ONLINE - SÀI GÒN
//                            '902448306510453', // Shop rẻ cho mẹ và bé
                        );
                        foreach ($groupIds as $groupId) {
                            $response = $fb->post("/{$groupId}/feed", $data);
                            $graphNode = $response->getGraphNode();
                            if (!empty($graphNode['id'])) {
                                $result[] = $groupId . ': ' . $graphNode['id'];
                            }
                        }
                    }
                    
                    $result = array(
                        'status' => 'OK',
                        'message' => implode('<br/>', $result),
                    );
                    die(\Zend\Json\Encoder::encode($result));
                } catch (Facebook\Exceptions\FacebookResponseException $e) {                 
                    $result = array(
                        'status' => 'OK',
                        'message' => 'ERR1: ' . $e->getMessage(),
                    );
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    $result = array(
                        'status' => 'OK',
                        'message' => 'ERR2' . $e->getMessage(),
                    );
                } catch (\Exception $e) {
                    Log::error(sprintf("Exception\n"
                        . " - Message : %s\n"
                        . " - Code : %s\n"
                        . " - File : %s\n"
                        . " - Line : %d\n"
                        . " - Stack trace : \n"
                        . "%s",
                        $e->getMessage(),
                        $e->getCode(),
                        $e->getFile(),
                        $e->getLine(),
                        $e->getTraceAsString()),
                    $param);
                    $result = array(
                        'status' => 'OK',
                        'message' => 'ERR3: ' . $e->getMessage(),
                    );
                } 
            }  
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
    /**
     * Ajax share to facecbook
     *
     * @return Zend\View\Model
     */
    public function shareAction()
    {   
        if (!$this->isAdmin()) {
            exit;
        }
        $AppUI = $this->getLoginInfo();
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost()
            && !empty($param['product_id'])
            && !empty($param['url'])) {
            $product = Products::getDetail($param['product_id']);
            if (empty($product)) {
                exit;
            }   
            if (empty($product['image_facebook'])) {
                $param['image_facebook'] = Util::uploadImageFromUrl($product['url_image'], 300, 300);                
            }
            $param['url'] = str_replace('.dev', '.com', $param['url']);
            $param['data'] = serialize($product);
            $result = Api::call('url_shareurls_add', $param);                 
            if (empty(Api::error())) { 
                if (!empty($param['image_facebook'])) {
                    Products::removeCache($param['product_id']);
                }
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
