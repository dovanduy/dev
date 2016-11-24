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
use Application\Lib\Arr;
use Application\Lib\Log;
use Web\Model\LocaleCities;
use Web\Model\LocaleStates;
use Web\Model\Products;
use Web\Lib\Api;
use Web\Lib\Fb;
use Web\Lib\Blogger;
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
                    'price' => app_money_format($result['price'], false),
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
     * Ajax remove a product from category
     *
     * @return Zend\View\Model
     */
    public function deleteproductdbAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost() 
            && !empty($param['product_id'])) {            
            $result = Api::call('url_products_delete', $param);           
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
     * Ajax share to email
     *
     * @return Zend\View\Model
     */
    public function emailshareAction()
    {   
        set_time_limit(0);
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
            $emailFile = \Application\Module::getConfig('send_email_file');
            if (!file_exists($emailFile)) {
                $data = array();
            } else {
                $content = file_get_contents($emailFile);
                if (!empty($content)) {
                    $data = unserialize($content);
                    if (!empty($data)) {
                        foreach ($data as $row) {
                            if ($row['product_id'] == $product['product_id']) {
                                $result = array(
                                    'status' => 'OK',
                                    'message' => 'Already existed',
                                );
                                die(\Zend\Json\Encoder::encode($result));	
                            }
                        }                
                    }
                }
            }            
            $param['url'] = str_replace('.dev', '.com', $param['url']);
            $param['url'] .= '?utm_source=google&utm_medium=email&utm_campaign=product';
            $param['short_url'] = Util::googleShortUrl($param['url']);
            $data[] = [       
                'category_id' => $product['category_id'],
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'short' => $product['short'],
                'price' => app_money_format($product['price']),
                'original_price' => app_money_format($product['original_price']),
                'discount_percent' => $product['discount_percent'],
                'discount_amount' => $product['discount_amount'],
                'url_image' => $product['url_image'],
                'url' => $param['url'],
                'short_url' => $param['short_url'],        
            ];
            if (file_put_contents($emailFile, serialize($data))) {
                $result = array(
                    'status' => 'OK',
                    'message' => 'Done',
                );
            } else {
                 $result = array(
                    'status' => 'OK',
                    'message' => 'Can not write file',
                );
            }
            die(\Zend\Json\Encoder::encode($result));          
        }
        exit;
    }
    
    /**
     * Ajax share to facecbook group
     *
     * @return Zend\View\Model
     */
    public function shareAction()
    {   
        set_time_limit(0);
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
                Api::call('url_products_updatefbimage', [
                    'products' => \Zend\Json\Encoder::encode([
                        [
                            'website_id' => $product['website_id'], 
                            'product_id' => $product['product_id'], 
                            'url_image' => $product['url_image'],
                            'name' => $product['name'],
                        ]
                    ])
                ]);
                Products::removeCache($product['product_id']);             
            }
            
            $param['url'] = str_replace('.dev', '.com', $param['url']);   
            $product['url'] = $param['url']  . '?utm_source=facebook&utm_medium=social&utm_campaign=product';
            $product['short_url'] = Util::googleShortUrl($product['url']);  
            $data = app_get_fb_share_content($product);
            
            $result = [];
            $shares = Api::call('url_productshares_all', [
                'product_id' => $product['product_id'],
                'group_only' => 1,
            ]);
            $groupIds = Arr::rand(app_facebook_groups(), 4);
            foreach ($groupIds as $groupId) {                
                if ($groupId == '378628615584963' && !in_array($AppUI->facebook_id, ['103432203421638'])) {
                    continue;
                }
                $shared = Arr::filter($shares, 'group_id', $groupId, false, false);                       
                if (!empty($shared)) {                    
                    $commentId = Fb::commentToPost($shared[0]['social_id'], app_get_fb_share_comment(), $AppUI->fb_access_token, $errorMessage);
                    if (!empty($commentId)) {
                        $result[] = "Group:{$groupId} - Comment:{$commentId}";
                    } else {
                        $result[] = "Group:{$groupId} - Comment:{$errorMessage}";
                    }
                    continue;
                }
                /*
                if (!empty($shared)) {
                    $socialId = $shared[0]['social_id'];
                    $ok = Fb::updatePost($socialId, $data, $AppUI->fb_access_token, $errorMessage);
                    if (!empty($ok)) {
                        $result[] = "Group:{$groupId} - Updated:{$socialId}";
                    } else {
                        $result[] = "Group:{$groupId} - Updated:{$socialId}";
                    }
                    continue;
                }
                */
                $id = Fb::postToGroup($groupId, $data, $AppUI->fb_access_token, $errorMessage);
                if (!empty($id)) {
                    Api::call('url_productshares_add', [
                        'user_id' => $AppUI->id,
                        'product_id' => $product['product_id'],
                        'facebook_id' => $AppUI->facebook_id,
                        'group_id' => $groupId,
                        'social_id' => $id
                    ]); 
                    if (empty(Api::error())) {
                        $result[] = "Group:{$groupId} - Post:{$id}";
                    } else {
                        $result[] = "Group:{$groupId} - Post:System Error";
                    }                    
                } else {
                    $result[] = "Group:{$groupId}: - Post:{$errorMessage}"; 
                }               
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
    public function fbshareAction()
    {   
        set_time_limit(0);
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
            $shares = Api::call('url_productshares_all', [
                'product_id' => $product['product_id'],                       
                'facebook_id' => $AppUI->facebook_id,                       
                'wall_only' => 1,
            ]);           
            if (!empty($shares)) {
//                foreach ($shares as $share) { 
//                    if ($share['owner_id'] != $AppUI->facebook_id) {                     
//                        $commentList = app_get_comment_list();                       
//                        $commentIcon = array_rand($commentList);    
//                        $commentMessage = $commentList[$commentIcon];
//                        $commentData = [
//                            'message' => $commentMessage,
//                            'attachment_url' => $commentIcon
//                        ];                       
//                        $commentMessage = $commentList[array_rand($commentList)];   
//                        $commentData = [
//                            'message' => $commentMessage                          
//                        ];
//                        
//                    } else {
//                        $commentData = [
//                            'message' => 'Cám ơn khách hàng đã ủng hộ'                          
//                        ];
//                    }
//                    $commentId = Fb::commentToPost($share['social_id'], $commentData, $AppUI->fb_access_token, $errorMessage);
//                    $result = array(
//                        'status' => 'OK',
//                        'message' => !empty($commentId) ? 'CommentId: ' . $commentId : $errorMessage,
//                    );
//                    die(\Zend\Json\Encoder::encode($result));
//                }               
            }            
           
            $param['url'] = str_replace('.dev', '.com', $param['url']);   
            $product['url'] = $param['url']  . '?utm_source=facebook&utm_medium=social&utm_campaign=product';
            $product['short_url'] = Util::googleShortUrl($product['url']);
            $product['tags'] = app_facebook_tags($AppUI->facebook_id);
            $data = app_get_fb_share_content($product);
                        
            if (!empty($shares)) {
                $ok = Fb::updatePost($shares[0]['social_id'], $data, $AppUI->fb_access_token, $errorMessage);
                if ($ok) {                    
                    $commentId = Fb::commentToPost($shares[0]['social_id'], app_get_fb_share_comment(), $AppUI->fb_access_token, $errorMessage);
                    if (!empty($commentId)) {
                        $result[] = "Comment:{$commentId}";
                    } else {
                        $result[] = "Comment:{$errorMessage}";
                    }
                    $result = array(
                        'status' => 'OK',
                        'message' => implode('<br/>', $result)
                    );
                    die(\Zend\Json\Encoder::encode($result));
                }                    
                exit;
            }
             
            $photos = array();            
            if (!empty($product['images'])) {
                if (!empty($product['colors'])) {                            
                    foreach ($product['colors'] as $color) {   
                        $photos[$color['url_image']] = $product['name'] . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name']));
                    }
                }
                foreach ($product['images'] as $image) {   
                    if (!isset($photos[$image['url_image']])) {
                        $photos[$image['url_image']] = $product['name'];
                    }
                }
            }
            $countUpload = 0;
            if (!empty($photos)) {
                $i = 0;
                foreach ($photos as $imageUrl => $name) { 
                    $photoData = [
                        'caption' => implode(PHP_EOL, [ 
                            $name,
                            mb_ereg_replace('!\s+!', ' ', $product['short'])
                        ]),
                        'url' => $imageUrl,
                    ];
                    $photoId = Fb::uploadUpublishedPhoto($photoData, $AppUI->fb_access_token, $errorMessage);
                    if (!empty($photoId)) {
                        $countUpload++;
                        $data["attached_media[{$i}]"] = '{"media_fbid":"' . $photoId . '"}';
                        $i++;
                    }
                }
            }
            if ($countUpload > 1) {
                unset($data['link']);
                unset($data['picture']);
            }
            $id = Fb::postToWall($data, $AppUI->fb_access_token, $errorMessage);
            if (!empty($id)) {
                Api::call('url_productshares_add', [
                    'product_id' => $product['product_id'],
                    'user_id' => $AppUI->id,
                    'facebook_id' => $AppUI->facebook_id,
                    'social_id' => $id
                ]);
            }
            $result = array(
                'status' => 'OK',
                'message' => !empty($id) ? $id : $errorMessage,
            );
            die(\Zend\Json\Encoder::encode($result));
            
            /*
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
            */
            
            
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
                $price .= ' (-' . $product['discount_percent'] . '%)';
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
    public function share1Action()
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
    
    /**
     * Ajax remove a product from category
     *
     * @return Zend\View\Model
     */
    public function sharebloggerAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $AppUI = $this->getLoginInfo();
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost() 
            && !empty($param['product_id'])) {      
            $product = Products::getDetail($param['product_id']);
            if (empty($product)) {
                exit;
            }
            if (empty($AppUI->google_access_token) || strtotime($AppUI->access_token_expires_at) < time()) {
                $result = array(
                    'status' => 'OK',
                    'message' => 'Invalid user or Token have been expired',
                );
                die(\Zend\Json\Encoder::encode($result));   
            }
            $data['name'] = $product['name'];
            if ($product['website_id'] == 1) {
                $siteUrl = 'http://vuongquocbalo.com';                
                $product['url'] = $siteUrl . '/' . name_2_url($product['name']) . '?utm_source=blogger&utm_medium=social&utm_campaign=product';
				$product['content'] = strip_tags($product['content'], '<p><div><span><ul><li><strong><b><br><center>');
				if (!empty($product['images'])) {
					foreach ($product['images'] as $image) { 
						$image['url_image'] = str_replace('.dev', '.com', $image['url_image']);
						$product['content'] .= "<center><p><img style=\"width:80%\" src=\"{$image['url_image']}\"/></p></center>";
					}    
				}
			} else {
                $siteUrl = 'http://thoitrang1.net';                
                $product['url'] = $siteUrl . '/' . name_2_url($product['name']) . '?utm_source=blogger&utm_medium=social&utm_campaign=product';
				$product['content'] = strip_tags($product['content'], '<p><div><span><ul><li><strong><b><br><center>');
                if (!empty($product['images'])) {
					foreach ($product['images'] as $image) { 
						$image['url_image'] = str_replace('.vn', '.net', $image['url_image']);
						$product['content'] .= "<center><p><img style=\"width:80%\" src=\"{$image['url_image']}\"/></p></center>";
					}    
				}
            }
            $blogs = [];
            $labels = [];
            if (!empty($product['categories'])) {
                foreach ($product['categories'] as $category) {
                    $labels[] = $category['name'];
                    $blogId = app_bloggers($category['category_id']);
					if (!empty($blogId)) {
						$blogs = array_merge($blogs, $blogId);
					}
                }
            }            
            if (array_intersect([15, 16], $product['category_id'])) {
                $data['content'] = implode('<br/>', [                
                        $product['content'],
                        "<center style=\"width:100%;color:#D4232B;font-size:30px;padding:5px;\"> Giá: " . app_money_format($product['price']) . '</center>',               
                        "<center><p><a href=\"{$product['url']}\"><img src=\"{$siteUrl}/web/images/buy_now.gif\"/></a></p></center>",		
                    ]
                );
                if (!empty($product['attributes'])) {
                    foreach ($product['attributes'] as $attribute) {
                        if (!empty($attribute['value'])) {
                            $labels[] = $attribute['value'];
                        }
                    } 
                }
            } else {
                $data['content'] = implode('<br/>', [                
                        $product['short'],
                        $product['content'],
                        "<center style=\"width:100%;color:#D4232B;font-size:30px;padding:5px;\"> Giá: " . app_money_format($product['price']) . '</center>',               
                        "<center><p><a href=\"{$product['url']}\"><img src=\"http://vuongquocbalo.com/web/images/buy_now.gif\"/></a></p></center>",		
                    ]
                );
            }
            $data['labels'] = $labels;
            $result = [];
			if (!empty($blogs)) {
				$blogs = array_unique($blogs);
				foreach ($blogs as $blogId) {
                    $check = Api::call('url_bloggerpostids_all', [
                        'product_id' => $product['product_id'],
                        'blog_id' => $blogId                
                    ]);
                    if (empty($check)) {
                        $postId = Blogger::post($blogId, $data, $AppUI->google_access_token, $errorMessage);
                        if (!empty($postId)) {
                            $result[$blogId] = $postId;
                            Api::call('url_bloggerpostids_add', [
                                'product_id' => $product['product_id'],
                                'blog_id' => $blogId,
                                'post_id' => $postId,                
                            ]);
                        } else {
                            $result[$blogId] = $errorMessage;
                        }
                    } else {
                        $result[$blogId] = 'Posted';
                    }
				}
				$result = array(
					'status' => 'OK',
					'message' => implode('<br/>', $result),
				);
			} else {
				$result = array(
					'status' => 'OK',
					'message' => 'Not found Blog ID',
				);
			}
            die(\Zend\Json\Encoder::encode($result));       
        }
        exit;
    }
    
    public function copyproductlzdAction()
    {
        include_once getcwd() . '/include/simple_html_dom.php';
        $request = $this->getRequest();  
        $param = $this->getParams(array(                      
            'product_id' => 0,            
            'category_id' => 0,   
            'small_size' => 0
        ));
        $id = $param['product_id'];
        $data = Products::getDetail($id, 1);
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        $data['code_copy'] = $data['code'];
        $data = $this->customProductForCopy($data, true, $param['small_size']);
        $data['size'] = strip_tags($data['size']);
        $data['package'] = explode('x', $data['size']);  
        $data['package_weight'] = $data['weight'] + 0.05; 
        $data['special_from_date'] = date('Y-m-d', time());
        $data['special_to_date'] = '2017-12-31';
        $data['js'] = preg_replace('/(\s\s+|\t|\n)/', '', "$('#main-name').val('{$data['name']}');
            $('#other_details-short_description').htmlarea('html','{$data['short']}');
            $('#other_details-description').htmlarea('html','{$data['content']}');
            $('#other_details-package_content').htmlarea('html','{$data['bo_san_pham']}');
            $('#main-brand').val('OEM');
            $('#main-production_country option[value=256]').attr('selected','selected');             
            $('#other_details-product_measures').val('{$data['size']}');
            $('#other_details-product_weight').val('{$data['weight']}');
            $('#other_details-package_height').val('{$data['package'][2]}');
            $('#other_details-package_length').val('{$data['package'][0]}');            
            $('#other_details-package_width').val('{$data['package'][1]}');
            $('#other_details-package_weight').val('{$data['package_weight']}');
            $('#other_details-warranty_type option[value=25348]').attr('selected','selected');                         
            if ($('#variations_0_variation').length > 0) {
                $('#variations_0_variation option[value=351]').attr('selected','selected');   
            }
            $('#create_form input[name=\"variations[0][sku_seller]\"]').val('{$data['code']}');
            $('#create_form input[name=\"variations[0][quantity]\"]').val('1');
            $('#create_form input[name=\"variations[0][price]\"]').val('{$data['price_src']}');
            $('#create_form input[name=\"variations[0][special_price]\"]').val('{$data['price']}');
            $('#create_form input[name=\"variations[0][special_from_date]\"]').val('{$data['special_from_date']}');
            $('#create_form input[name=\"variations[0][special_to_date]\"]').val('{$data['special_to_date']}');
            $('html, body').animate({ scrollTop: $('#main-color_family').offset().top}, 100);");
        return $this->getViewModel(array(
                'data' => $data
            )
        );
        
    }
    
    public function copyproductlzd1Action()
    {
        include_once getcwd() . '/include/simple_html_dom.php';
        $request = $this->getRequest();  
        $param = $this->getParams(array(                      
            'product_id' => 0,            
            'category_id' => 0,            
        ));
        $id = $param['product_id'];
        $data = Products::getDetail($id, 1);
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        $data['tags'] = [];
        if (!empty($data['categories'])) {            
            foreach ($data['categories'] as $category) {                
                if ($data['website_id'] == 1) {      
                    if ($category['category_id'] == 16) {          
                        $category['name'] = 'Túi rút';
                    }
                    $category['name'] = str_replace('Ba Lô', 'balo', $category['name']);
                }
                $data['tags'][] = $category['name'];
            }            
        }
        $size = '';
        if (!empty($data['attributes'])) {            
            foreach ($data['attributes'] as $attribute) {
                if (in_array($attribute['field_id'], [7,9]) && empty($size)) {                                    
                    $size = $attribute['value'];
                }
                if ($attribute['field_id'] == 1) {
                    $data['tags'][] = $attribute['name'] . ' ' . $attribute['value'];
                }
            }
        }
        $data['tags'] = strtolower(implode(',', $data['tags'])); 
        if (!empty($data['more']) && $data['website_id'] == 1) {            
            $html = str_get_html($data['more']);
            foreach($html->find('p') as $element) {
                if (strpos(strtolower($element->innertext), 'cm') !== false) {
                    $data['size'] = $element->innertext;
                    $data['size'] = str_replace("&nbsp;", " ", $data['size']);
                    $data['size'] = preg_replace("/\s+/", " ", $data['size']);
                    $data['size'] = str_replace('Kích thước:', '', $data['size']);
                    $data['size'] = str_replace('Kích thước', '', $data['size']);                    
                    $data['size'] = trim(html_entity_decode($data['size']), " \t\n\r\0\x0B\xC2\xA0");
                    $data['size'] = trim($data['size']);
                    if ($data['size'] != $size) {
                        if (Api::call('url_products_updatesizeattr', [
                            'category_id' => $param['category_id'], 
                            'product_id' => $id, 
                            'field_id' => $data['website_id'] == 1 ? 7 : 9, 
                            'value' => $data['size']])) {
                            Products::removeCache($id);   
                            $data = Products::getDetail($id);
                        }
                    }
                    break;
                }
            }
        }
        $data['name'] = str_replace(['&'], ['and'], $data['name']);        
        if (array_intersect([15], $data['category_id'])) {   
            if (empty($param['small_size'])) {
                $data['price'] = '189000';
                $data['price_src'] = '220000';
                $data['short'] = implode(PHP_EOL, [                        
                        'Hàng Việt Nam xuất khẩu.',  
                        'Chất liệu simili 100% không thấm nước, không bong tróc. dễ lau chùi khi bị bẩn.',               
                        'Công nghệ in Nhật Bản cho hình in đẹp.',
                        'Phù hợp cho Teen, Học sinh cấp 1, cấp 2, cấp 3.',
                    ]
                );
                $data['content'] = implode(PHP_EOL, [
                        '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                        '- Balo lớn kích thước ngang 32 x cao 41.5 x rộng 14.5 (cm).',                        
                        '- Có 2 ngăn để vừa laptop 14", có chổ để bình nước.',               
                        '- Phù hợp đựng tập vở cho học sinh cấp 1, cấp 2, cấp 3, đựng đồ đi chơi.',
                        '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.', 
                    ]
                );
                $data['size'] = '32x14.5x41.5';
                $data['weight'] = '0.5';
                $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                        '1 x balo',                       
                    ]
                );
            } else {
                $data['price'] = '169000';
                $data['price_src'] = '199000';
                $data['short'] = implode(PHP_EOL, [  
                        'Thiết kế 2 trong 1 tiện lợi, vừa có thể đeo vai như balo vừa có thể đeo chéo',  
                        'Hàng Việt Nam xuất khẩu.',  
                        'Chất liệu simili 100% không thấm nước, không bong tróc. dễ lau chùi khi bị bẩn.',               
                        'Công nghệ in Nhật Bản cho hình in đẹp.',
                    ]
                );
                $data['content'] = implode(PHP_EOL, [ 
                        '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                        '- Balo nhỏ xinh xắn, kích thước ngang 26 x cao 32 x rộng 9 (cm).',                       
                        '- Có 1 ngăn lớn và ngăn đựng vừa Laptop 12"/Ipad',
                        '- Phù hợp đựng tập vở, tài liệu, giấy A4, Laptop 12"/Ipad đi học thêm, đi làm, đi chơi, đựng đồ cho bé đi nhà trẻ, mẫu giáo',
                        '- Dây đeo tháo rời, thiết kế 2 trong 1 tiện lợi, vừa có thể đeo vai như balo vừa có thể đeo chéo.',
                        '- Balo giả da simili hàng Việt Nam xuất khẩu, chất lượng đảm bảo không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',                                       
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng',                                          
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',                        
                    ]
                );
                $data['size'] = '26x9x32';
                $data['weight'] = '0.4';                
                $data['bo_san_pham'] = implode('<br>', [                                        
                        '1 x balo',
                        '1 x dây đeo chéo',
                    ]
                );                
            }
            $data['material'] = 'Simili';
        } elseif (array_intersect([16], $data['category_id'])) {
            $data['price'] = '69000';
            $data['price_src'] = '99000';
            $data['short'] = implode(PHP_EOL, [  
                    'Hàng Việt Nam xuất khẩu.',  
                    'Chất liệu simili 100% không thấm nước, không bong tróc. dễ lau chùi khi bị bẩn.',               
                    'Công nghệ in Nhật Bản cho hình in đẹp.',
                    'Phù hợp đựng tập vở đi học thêm hoặc đựng đồ đi chơi.', 
                ]
            );            
            $data['content'] = implode(PHP_EOL, [  
                    '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                    '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',    
                    '- Kích thước 29 x 40 (cm).',
                    '- Phù hợp đựng tập vở đi học thêm hoặc đựng đồ đi chơi.',                               
                    '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                    '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                    '- Màu dây giao ngẫu nhiên, có 2 màu đen hoặc trắng.',
                    '- Balo có 1 mặt in như hình và 1 mặt trơn màu đen sang trọng.',
                ]
            );
            $data['size'] = '29x2x40';
            $data['weight'] = '0.25';
            $data['material'] = 'Simili';
            $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                    '1 x túi rút',                       
                ]
            );
        } elseif (array_intersect([99], $data['category_id'])) {
            $data['short'] = implode(PHP_EOL, [  
                    'Hàng Việt Nam xuất khẩu.',  
                    'Chất liệu simili 100% không thấm nước, không bong tróc. dễ lau chùi khi bị bẩn.',               
                    'Công nghệ in Nhật Bản cho hình in đẹp.',
                    'Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm, các vật dụng cá nhân cho nữ.', 
                ]
            );
            $data['content'] = implode(PHP_EOL, [ 
                    '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                    '- <strong>Túi chéo nữ mini kích thước ngang 24 x 17 (cm)</strong>.',    
                    '- Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm, các vật dụng cá nhân cho nữ, ...',    
                    '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                    '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                ]
            ); 
            $data['price'] = '79000';
            $data['price_src'] = '99000';
            $data['weight'] = '0.2';
            $data['size'] = '24x2x17';
            $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                    '1 x túi chéo',                       
                ]
            );
         } elseif (array_intersect([8], $data['category_id'])) {
            $data['price'] = '119000'; 
            $data['price_src'] = '179000';
            $data['content'] = strip_tags($data['content'], '<b><strong><div><span><br><ul><li>');
            $data['short'] = implode(PHP_EOL, [  
                    'Hàng Việt Nam xuất khẩu.',  
                    'Chất liệu simili 100% không thấm nước, không bong tróc. dễ lau chùi khi bị bẩn.',               
                    'Công nghệ in Nhật Bản cho hình in đẹp.',
                    'Phù hợp đựng tập vở, tài liệu, giấy A4 hoặc máy tính bảng.', 
                ]
            );
            $data['content'] = implode(PHP_EOL, [ 
                    '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                    '- <strong>Túi chéo kích thước ngang 34 x cao 25 x rộng 9 (cm)</strong>.',    
                    '- Sử dụng đựng tập vở, tài liệu, giấy A4 hoặc máy tính bảng.',    
                    '- Có 1 ngăn lớn, phù hợp đi học thêm, đi làm, đi chơi.',                                                       
                    '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                    '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                ]
            );
            $data['size'] = '34x9x25'; 
            $data['weight'] = '0.35';
            $data['material'] = 'Simili';
            $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                    '1 x túi chéo',                       
                ]
            );
        } elseif (array_intersect([102], $data['category_id'])) {
            $data['price'] = '150000';
            $data['content'] = strip_tags($data['content'], '<b><strong><div><span><br><ul><li>');
            $data['content'] = implode(PHP_EOL, [
                "<p>- Chiếc gối là một vật dụng thân thiết gắn liền với cuộc sống hằng ngày của chúng ta. Ngày nay kiểu dáng của những chiếc gối đã không còn nằm trong khuôn khổ cũ. Mẫu mã được thanh đổi liên tục và phù hợp với trào lưu. Gối in hình vừa có thể dùng vừa làm quà tặng tới bạn bè, người thân đều được!</p>",
                "<p>- Giấc ngủ của bạn sẽ trở nên êm đềm hơn khi trên chiếc gối in hình ảnh mà bạn yêu thích. Hình những con vật dễ thương, những nhân vật hoạt hình mà bạn yêu thích, ca sĩ nhóm nhạc mà bạn thần tượng….. tất cả đều có thể in lên chiếc gối quen thuộc của bạn.</p>",
                "",
                "<p>- <strong>Kích thước gối ngang 38 x cao 7 x rộng 38 (cm)</strong></p>",
                "<p>- Chất liệu vải nhung mịn, ruột gối bên trong là gòn bi không xẹp, in hình độc đáo</p>",             
                "<p>- Công nghệ in Nhật Bản, bền màu trong thời gian dài sử dụng.</p>",
            ]);
            $data['size'] = 'Ngang 38 x cao 7 x rộng 38 (cm)';
            $data['weight'] = '500';
            $data['material'] = 'Vải nhung';
        } else {
//            $data['price'] = $data['price_src'] - round((5/100)*$data['price_src'], -3);
//            $data['price'] = substr_replace($data['price'], '9', -4, -3);
//            $data['price_src'] = $data['price_src'] + round((20/100)*$data['price_src'], -3);
            
            if (empty($data['price_src'])) {
                $data['price_src'] = $data['original_price'];
            }
            $data['content'] = strip_tags($data['content'], '<p><b><strong><div><span><br><ul><li>');
            $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                    '1 x sản phẩm',                       
                ]
            ); 
        }
        if (!empty($data['attributes'])) {         
            if (!empty($data['attributes']) && !array_intersect([15, 16, 8, 99], $data['category_id'])) {  
                $data['content'] = '';
                $data['content'] .= '<strong>THÔNG TIN SẢN PHẨM:</strong>' . PHP_EOL;
                $data['content'] .= '<ul>';               
                foreach ($data['attributes'] as &$attribute) {
                    if ($attribute['field_id'] == 15) {
                        if (!empty($param['small_size'])) {
                            $attribute['value'] = 400;
                        }
                        $attribute['name'] = 'Trọng lượng (Kg)';
                        $attribute['value'] = ($attribute['value'] / 1000);
                        $data['weight'] = $attribute['value'];
                    }
                    if (!empty($attribute['value'])) {
                        $data['content'] .= '<li>' . $attribute['name'] . ': ' . $attribute['value'] . '</li>'; 
                    }
                    if ($attribute['name'] == 'Màu sắc') {
                        $data['name'] .= ' (' . $attribute['value'] . ')';
                    }
                    if ($attribute['field_id'] == 7) {
                        $data['size'] = $attribute['value'];
                    }
                }
                $data['content'] .= '</ul>';
            } else {
                $renamed = false;
                foreach ($data['attributes'] as &$attribute) {
                    if ($renamed == false && array_intersect([15], $data['category_id']) && $attribute['field_id'] == 1) {
                        if (empty($param['small_size'])) {                            
                            $data['name'] = str_replace(['BL '], ['Balo học sinh ' . ($attribute['value']) . ' '], $data['name']);
                        } else {
                            $newCode = $data['code'] . '-S';  
                            $data['code_copy'] = $data['code'];
                            $data['name'] = str_replace(['BL '], ['Balo & túi đeo chéo 2 trong 1 ' . ($attribute['value']) . ' '], $data['name']);
                            $data['code'] = $newCode;                            
                        }
                        $renamed = true;
                    } elseif ($renamed == false && array_intersect([16], $data['category_id']) && $attribute['field_id'] == 1) {
                        $data['name'] = str_replace(['Túi rút '], ['Túi Rút ' . ($attribute['value']) . ' '], $data['name']);
                        $renamed = true;
                    } elseif ($renamed == false && array_intersect([99], $data['category_id']) && $attribute['field_id'] == 1) {
                        $data['name'] = str_replace(['Túi chéo nữ '], ['Túi chéo nữ mini ' . ($attribute['value']) . ' '], $data['name']);
                        $renamed = true;                    
                    } elseif ($renamed == false && array_intersect([8], $data['category_id']) && $attribute['field_id'] == 1) {
                        $data['name'] = str_replace(['Túi chéo '], ['Túi chéo ' . ($attribute['value']) . ' '], $data['name']);
                        $renamed = true;
                    }
                    if (!empty($attribute['value'])) {
                        $data['content'] .= PHP_EOL . '- ' . $attribute['name'] . ': ' . $attribute['value']; 
                    }
                }
            }
        }      
        
        $data['content'] .= '<br/><strong>HÌNH ẢNH SẢN PHẨM:</strong>' . PHP_EOL;
        foreach ($data['images'] as $image) {
            $data['content'] .= '<br/><center><img src="' . $image['url_image'] . '" /></center>';
        }
        if (!empty($data['more'])) {            
            $data['more'] = preg_replace('#(<br */?>\s*)+#i', '<br>', $data['more']); 
            $data['more'] = strip_tags($data['more'], '<center><div><strong><br><p><b>');
            $data['more'] = str_replace(
                [
                    'vuongquocbalo.com', 
                    'thoitrang1.net',
                    '<p></p>',
                    'website'
                ], 
                [
                    '<strong>vuongquocbalo</strong>', 
                    '<strong>vuongquocbalo</strong>',
                    '',
                    '',
                ],
                $data['more']
            );
            $data['content'] .= PHP_EOL . '<strong>GIỚI THIỆU SẢN PHẨM ' . mb_strtoupper($data['name']) . ':</strong>' . PHP_EOL . $data['more'] . PHP_EOL;
        }
        
        /*
        $data['content'] = preg_replace('/(\s\s+|\t)/', ' ',$data['content']);
        $data['content'] = str_replace(['<br>','<p>','<p style="text-align: justify;">','<li style="text-align: justify;">','</p>'], [PHP_EOL,PHP_EOL,PHP_EOL,PHP_EOL,''], $data['content']);            
        if (array_intersect([1,2,3,4,5,6], $data['category_id'])) { 
            $data['weight'] = 400;
            $data['seo'] = 'túi xách, cặp táp nam nữ, túi chéo, túi đeo chéo, túi chéo sinh viên, balo nam nữ, bóp ví nam nữ';           
        } elseif (array_intersect([20,21], $data['category_id'])) {
            $data['weight'] = 150;
            $data['seo'] = 'túi xách, cặp táp nam nữ, túi chéo, túi đeo chéo, túi chéo sinh viên, balo nam nữ, bóp ví nam nữ';           
        } elseif (array_intersect([69,70], $data['category_id'])) {
            $data['weight'] = 300;
            $data['seo'] = 'đầm suông, đầm xòe, đầm maxi, đầm ôm, đầm dạ hội, đầm xếp ly, đầm denim, đầm trễ vai, đầm hai dây';           
        } */ 
        $data['name'] = str_replace("'", '', $data['name']);
        $data['name'] = preg_replace('/(\s\s+|\t|\n)/', ' ', $data['name']);
        
        $data['content'] = implode('<br/>', [
            "<strong style=\"color:red\">TIÊU CHÍ BÁN HÀNG CỦA VUONGQUOCBALO</strong>",
            "- Không bán sản phẩm kém chất lượng.",
            "- Giá cạnh tranh nhất, quý khách nên so sánh giá trước khi quyết định đặt hàng.",
            "- Cam kết hàng giống hình.",
            "- Sản phẩm mô tả đầy đủ màu sắc, kích thước, chất liệu, quý khách vui lòng xem mô tả sản phẩm.",
            "",
            str_replace([PHP_EOL,"'"], ['<br>',''], $data['content']),            
            "",
            "<strong>HÌNH ẢNH SẢN PHẨM:</strong>",
            "",
            "",
        ]);
        $data['url_image'] = str_replace('vuongquocbalo.dev', 'vuongquocbalo.com', $data['url_image']);
        if (array_intersect([15, 8], $data['category_id'])) {            
            foreach ($data['images'] as $image) {
                $data['content'] .= '<br/><div style="text-align:center"><img src="' . $image['url_image'] . '" /></div>';
            }
            if (array_intersect([8], $data['category_id'])) {
                $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/tui_cheo_1.png" /></div>';
                $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/tui_cheo_2.png" /></div>';
            }      
            if (array_intersect([15], $data['category_id'])) {
                if (empty($param['small_size'])) {
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_lon_1.png" /></div>';
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_lon_2.png" /></div>';
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_lon_3.png" /></div>';
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_lon_4.png" /></div>';
                } else {
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_nho_1.png" /></div>';
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_nho_2.png" /></div>';
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_nho_3.png" /></div>';
                    $data['content'] .= '<br/><div style="text-align:center"><img src="http://img.vuongquocbalo.com/model/balo_nho_4.png" /></div>';
                }
            }
        } elseif (array_intersect([16, 99], $data['category_id'])) {
            $data['content'] .= '<br/><div style="text-align:center"><img src="' . $data['url_image'] . '" /></div>';
            if (array_intersect([99], $data['category_id'])) {
                $data['content'] .= '<br/><center><img src="http://img.vuongquocbalo.com/model/tui_cheo_mini_1.png" /></center>';
                $data['content'] .= '<br/><center><img src="http://img.vuongquocbalo.com/model/tui_cheo_mini_2.png" /></center>';
                $data['content'] .= '<br/><center><img src="http://img.vuongquocbalo.com/model/tui_cheo_mini_3.png" /></center>';
            }
            if (array_intersect([16], $data['category_id'])) {
                $data['content'] .= '<br/><center><img src="http://img.vuongquocbalo.com/model/tui_rut_1.png" /></center>';
                $data['content'] .= '<br/><center><img src="http://img.vuongquocbalo.com/model/tui_rut_2.png" /></center>';
                $data['content'] .= '<br/><center><img src="http://img.vuongquocbalo.com/model/tui_rut_3.png" /></center>';
            }
        }
        $data['content_html'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['content']);
        $data['content_html'] = htmlspecialchars($data['content_html']);
        $data['content_html'] = app_compress_content($data['content_html']);
        $data['short'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['short']);
        $data['short'] = htmlspecialchars($data['short']);
        $data['short'] = app_compress_content($data['short']);
        if (empty($data['code_copy'])) {
            $data['code_copy'] = $data['code'];
        }        
        $data['size'] = strip_tags($data['size']);
        $data['package'] = explode('x', $data['size']);  
        $data['package_weight'] = $data['weight'] + 0.05; 
        $data['special_from_date'] = date('Y-m-d', time());
        $data['special_to_date'] = '2017-12-31';
        $data['js'] = preg_replace('/(\s\s+|\t|\n)/', '', "$('#main-name').val('{$data['name']}');
            $('#other_details-short_description').htmlarea('html','{$data['short']}');
            $('#other_details-description').htmlarea('html','{$data['content_html']}');
            $('#other_details-package_content').htmlarea('html','{$data['bo_san_pham']}');
            $('#main-brand').val('OEM');
            $('#main-production_country option[value=256]').attr('selected','selected');             
            $('#other_details-product_measures').val('{$data['size']}');
            $('#other_details-product_weight').val('{$data['weight']}');
            $('#other_details-package_height').val('{$data['package'][2]}');
            $('#other_details-package_length').val('{$data['package'][0]}');            
            $('#other_details-package_width').val('{$data['package'][1]}');
            $('#other_details-package_weight').val('{$data['package_weight']}');
            $('#other_details-warranty_type option[value=25348]').attr('selected','selected');                         
            if ($('#variations_0_variation').length > 0) {
                $('#variations_0_variation option[value=351]').attr('selected','selected');   
            }
            $('#create_form input[name=\"variations[0][sku_seller]\"]').val('{$data['code']}');
            $('#create_form input[name=\"variations[0][quantity]\"]').val('1');
            $('#create_form input[name=\"variations[0][price]\"]').val('{$data['price_src']}');
            $('#create_form input[name=\"variations[0][special_price]\"]').val('{$data['price']}');
            $('#create_form input[name=\"variations[0][special_from_date]\"]').val('{$data['special_from_date']}');
            $('#create_form input[name=\"variations[0][special_to_date]\"]').val('{$data['special_to_date']}');
            $('html, body').animate({ scrollTop: $('#main-color_family').offset().top}, 100);");
        return $this->getViewModel(array(
                'data' => $data
            )
        );
    }
    
    public function getProductName(&$data, $lazada = false, $smallSize = false)
    {
        $renamed = false;
        foreach ($data['attributes'] as $attribute) {
            if ($renamed == false && array_intersect([15], $data['category_id']) && $attribute['field_id'] == 1) {
                if (!$smallSize) {
                    $data['name'] = str_replace(['BL '], ['Balo học sinh ' . ($attribute['value']) . ' '], $data['name']);
                } else {
                    $data['code'] = $data['code'] . '-S';
                    $data['name'] = str_replace(['BL '], ['Balo Và Túi đeo chéo 2 trong 1 ' . ($attribute['value']) . ' '], $data['name']);
                }
                $renamed = true;
            } elseif ($renamed == false && array_intersect([16], $data['category_id']) && $attribute['field_id'] == 1) {
                if ($lazada) {
                    $data['name'] = str_replace(['Túi rút '], ['Túi rút ' . strtolower($attribute['value']) . ' '], $data['name']);
                } else {
                    $data['name'] = str_replace(['Túi rút '], ['Balo dây rút - Túi rút ' . strtolower($attribute['value']) . ' '], $data['name']);
                    if (strlen($data['name']) > 70) {
                        $data['name'] = 'Balo dây rút - Túi rút ' .  strtolower($attribute['value']) . ' - ' . $data['code'];
                    }
                }
                $renamed = true;
            } elseif ($renamed == false && array_intersect([8], $data['category_id']) && $attribute['field_id'] == 1) {
                $data['name'] = str_replace(['Túi chéo '], ['Túi chéo ' . strtolower($attribute['value']) . ' '], $data['name']);
                $renamed = true;
            } elseif ($renamed == false && array_intersect([99], $data['category_id']) && $attribute['field_id'] == 1) {
                $data['name'] = str_replace(['Túi chéo nữ '], ['Túi chéo nữ mini ' . ($attribute['value']) . ' '], $data['name']);
                $renamed = true;                    
            }            
        }
        return $data;
    }
    
    public function customProductForCopy(&$data, $lazada = false, $smallSize = false)
    {
        $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                '1 x sản phẩm',                       
            ]
        );        
        if (empty($data['price_src']) && !empty($data['original_price'])) {
            $data['price_src'] = $data['original_price'];
        }
        $phongcachAttr = '';
        foreach ($data['attributes'] as $attribute) {            
            if ($attribute['field_id'] == 1 && !empty($attribute['value'])) {
                $phongcachAttr = $attribute['value'];
            }
            switch ($attribute['field_id']) {
                case 15: // Trọng lượng
                    if ($smallSize) {
                        $attribute['value'] = 400;
                    }
                    $data['weight'] = $attribute['value'];
                    break;
                case 7: // Kích thước (D x R x C)
                    if ($smallSize) {
                        $attribute['value'] = '26x9x32';
                    }
                    $data['size'] = $attribute['value'];
                    break;
                case 3: // Chất liệu
                    $data['material'] = $attribute['value'];
                    break;                
            }
        }
        if (array_intersect([15], $data['category_id'])) {   
           if (!$smallSize) {               
                $data['short'] = implode(PHP_EOL, [                                                
                        'Phù hợp cho Teen, Học sinh cấp 1, cấp 2, cấp 3.',                        
                        'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                        'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
                    ]
                );
                $data['content'] = implode(PHP_EOL, [                        
                        '- Balo lớn kích thước ngang 32 x cao 41.5 x rộng 14.5 (cm).',                        
                        '- Có 2 ngăn để vừa laptop 14", có chổ để bình nước.',               
                        '- Phù hợp đựng tập vở cho học sinh cấp 1, cấp 2, cấp 3, đựng đồ đi chơi.',
                        '- Chất liệu simili 100% giả da, không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Balo có 1 mặt in như hình và 1 mặt trơn màu đen sang trọng.',
                        '- Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                    ]
                );
                if (!empty($phongcachAttr)) {
                    $data['name'] = str_replace(['BL '], ['Balo học sinh ' . ($phongcachAttr) . ' '], $data['name']);
                }
                $data['price'] = '189000';
                $data['price_src'] = '220000';
                $data['size'] = '32x14.5x41.5';
                $data['weight'] = '500';
                if (!$lazada) {
                    $data['weight'] = '540';
                }
                $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                        '1 x balo',                       
                    ]
                );
           } else {               
                $data['short'] = implode(PHP_EOL, [  
                        'Thiết kế 2 trong 1 tiện lợi, vừa có thể đeo vai như balo vừa có thể đeo chéo',
                        'Phù hợp đựng máy tính bảng, tập vở đi học thêm.', 
                        'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                        'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
                    ]
                );
                $data['content'] = implode(PHP_EOL, [                         
                        '- Balo nhỏ xinh xắn, kích thước ngang 26 x cao 32 x rộng 9 (cm).',                       
                        '- Có 1 ngăn lớn và ngăn đựng vừa máy tính bảng',
                        '- Phù hợp đựng máy tính bảng, tập vở, tài liệu, giấy A4, đi học thêm, đi làm, đi chơi, đựng đồ cho bé đi nhà trẻ, mẫu giáo',
                        '- Dây đeo tháo rời, thiết kế 2 trong 1 tiện lợi, vừa có thể đeo vai như balo vừa có thể đeo chéo.',
                        '- Chất liệu simili 100% giả da, không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                    ]
                );
                $data['code'] = $data['code'] . '-S';
                if (!empty($phongcachAttr)) {
                    $data['name'] = str_replace(['BL '], ['Balo và Túi đeo chéo 2 trong 1 ' . ($phongcachAttr) . ' '], $data['name']);
                }
                $data['price'] = '169000';
                $data['price_src'] = '199000';    
                $data['size'] = '26x9x32';
                $data['weight'] = '400';                
                $data['bo_san_pham'] = implode('<br>', [                                        
                        '1 x balo',
                        '1 x dây đeo chéo',
                    ]
                );                
           }
           $data['material'] = 'Simili';
        } elseif (array_intersect([16], $data['category_id'])) {           
           $data['short'] = implode(PHP_EOL, [                      
                   'Phù hợp đựng tập vở đi học thêm hoặc đựng đồ đi chơi.', 
                   'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                   'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
               ]
           );            
           $data['content'] = implode(PHP_EOL, [                    
                   '- Kích thước ngang 29 x cao 40 (cm).',
                   '- Phù hợp đựng tập vở đi học thêm, đựng đồ đi chơi.',                                                                       
                   '- Túi có 1 mặt in như hình và 1 mặt trơn màu đen sang trọng.',
                   '- Chất liệu simili 100% giả da, không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                   '- Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                   '- <strong>Lưu ý</strong>: Màu dây giao ngẫu nhiên, có 2 màu đen hoặc trắng.',
               ]
           );
            if (!empty($phongcachAttr)) {
                if ($lazada) {
                    $data['name'] = str_replace(['Túi rút '], ['Túi rút ' . $phongcachAttr . ' '], $data['name']);
                } else {
                    $data['name'] = str_replace(['Túi rút '], ['Balo dây rút - Túi rút ' . $phongcachAttr . ' '], $data['name']);
                    if (strlen($data['name']) > 70) {
                        $data['name'] = 'Balo dây rút - Túi rút ' . $phongcachAttr . ' - ' . $data['code'];
                    }
                }
            }
            $data['price'] = '69000';
            $data['price_src'] = '99000';
            $data['size'] = '29x2x40';
            $data['weight'] = '250';
            $data['material'] = 'Simili';
            $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                    '1 x túi rút',                       
                ]
            );
        } elseif (array_intersect([99], $data['category_id'])) {
           $data['short'] = implode(PHP_EOL, [                      
                   'Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm cho nữ.', 
                   'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                   'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
               ]
           );             
           $data['content'] = implode(PHP_EOL, [                    
                   '- Túi chéo nữ mini kích thước ngang 24 x cao 17 (cm).',    
                   '- Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm, các vật dụng cá nhân cho nữ, ...',    
                   '- Chất liệu simili 100% giả da, không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                   '- Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
               ]
           );
           if (!empty($phongcachAttr)) {
               $data['name'] = str_replace(['Túi chéo nữ '], ['Túi chéo nữ mini ' . ($phongcachAttr) . ' '], $data['name']);
           }
           $data['price'] = '79000';
           $data['price_src'] = '99000';
           $data['weight'] = '200';
           $data['size'] = '24x2x17';
           $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                   '1 x túi chéo',                       
               ]
           );
        } elseif (array_intersect([8], $data['category_id'])) {            
           $data['short'] = implode(PHP_EOL, [               
                   'Phù hợp đựng tập vở, tài liệu, giấy A4, máy tính bảng.', 
                   'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                   'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
               ]
           );
           $data['content'] = implode(PHP_EOL, [                  
                   '- Túi chéo kích thước ngang 34 x cao 25 x rộng 9 (cm).',    
                   '- Sử dụng đựng tập vở, tài liệu, giấy A4 hoặc máy tính bảng.',    
                   '- Có 1 ngăn lớn, phù hợp đi học thêm, đi làm, đi chơi.',                                                       
                   '- Chất liệu simili 100% giả da, không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                   '- Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
               ]
           );
           if (!empty($phongcachAttr)) {
                $data['name'] = str_replace(['Túi chéo '], ['Túi chéo ' . $phongcachAttr . ' '], $data['name']);
           }
           $data['price'] = '119000'; 
           $data['price_src'] = '179000';
           $data['weight'] = '350';
           $data['size'] = '34x9x25';
           $data['bo_san_pham'] = implode(PHP_EOL, [                                        
                   '1 x túi chéo',                       
               ]
           );
           $data['original_content'] = $data['content'];
        } else {
           $data['original_content'] = $data['content'];
        }
        if (!isset($data['original_content'])) {
            $data['original_content'] = $data['content'];
        }
        $data['content'] = PHP_EOL . '<strong>THÔNG TIN SẢN PHẨM:</strong>';
        foreach ($data['attributes'] as $attribute) {
            if (!empty($attribute['value'])) {
                if ($attribute['field_id'] == 4 && !$smallSize && array_intersect([15], $data['category_id'])) {  
                    $attribute['value'] = 'Đeo vai';
                }
                $data['content'] .= PHP_EOL . '- ' . $attribute['name'] . ': ' . $attribute['value']; 
            }            
        } 
        if ($lazada) {
            $data['weight'] = ($data['weight'] / 1000);
            $images = [];
            if (array_intersect([15, 8], $data['category_id'])) {            
                foreach ($data['images'] as $image) {
                    $images[] = $image['url_image'];
                }
                if (array_intersect([8], $data['category_id'])) {
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_1.png';
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_2.png';
                }      
                if (array_intersect([15], $data['category_id'])) {
                    if (empty($smallSize)) {
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_lon_1.png';
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_lon_2.png';
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_lon_3.png';
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_lon_4.png';
                    } else {
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_1.png';
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_2.png';
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_3.png';
                        $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_4.png';
                    }
                }
            } elseif (array_intersect([16, 99], $data['category_id'])) {
                $images = [];
                $images[] = $data['url_image'];
                if (array_intersect([99], $data['category_id'])) {
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_mini_1.png';
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_mini_2.png';
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_mini_3.png';
                } elseif (array_intersect([16], $data['category_id'])) {
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_rut_1.png';
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_rut_2.png';
                    $images[] = 'http://img.vuongquocbalo.com/model/tui_rut_3.png';
                }
            }
            $data['content'] = PHP_EOL . PHP_EOL . "<strong>HÌNH ẢNH SẢN PHẨM:</strong>";
            foreach ($images as $imageUrl) {
                $data['content'] .= PHP_EOL . "<img src='{$imageUrl}' />";
            }
        } else {
            $data['content'] .= PHP_EOL . PHP_EOL . PHP_EOL . "<strong>HÌNH ẢNH SẢN PHẨM:</strong>";
            $data['price'] = $data['price_src'];
        }
        $data['original_content'] = strip_tags($data['original_content'], '<center><div><strong><br><b>');
        $data['original_content'] = preg_replace('#(<br */?>\s*)+#i', '<br>', $data['original_content']);
        $data['content'] .= PHP_EOL . PHP_EOL . '<strong>MÔ TẢ SẢN PHẨM:</strong>' . PHP_EOL . $data['original_content'];                
        $data['policy'] = implode(PHP_EOL, [
            "<strong style=\"color:red\">TIÊU CHÍ BÁN HÀNG:</strong>",
            "- Không bán sản phẩm kém chất lượng.",
            "- Giá cả cạnh tranh nhất, quý khách nên so sánh giá sản phẩm trước khi quyết định đặt hàng.",
            "- Cam kết hàng giống hình.",
            "- Sản phẩm mô tả đầy đủ màu sắc, kích thước, chất liệu, quý khách vui lòng xem mô tả sản phẩm.",
        ]);
        $data['content'] = $data['policy'] . PHP_EOL . PHP_EOL . $data['content'];
        $data['content'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['content']);
        $data['content'] = app_compress_content(htmlspecialchars($data['content'])); 
        
        $data['short'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['short']);
        $data['bo_san_pham'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['bo_san_pham']);
        
        $data['name'] = htmlspecialchars($data['name']);
        $data['name'] = str_replace(["’","'"], ['',''], $data['name']); 
        $data['content'] = str_replace(["’","'"], ['',''], $data['content']); 
        
        return $data;
    }
    
    /**
     * Copy Product
     *
     * @return Zend\View\Model
     */
    public function copyproductAction()
    {
        $request = $this->getRequest();  
        $param = $this->getParams(array(                      
            'product_id' => 0,            
            'category_id' => 0,  
            'small_size' => 0
        ));
        $id = $param['product_id'];
        $data = Products::getDetail($id, 1);
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        $data['code_copy'] = $data['code'];
        $data = $this->customProductForCopy($data, false, $param['small_size']);
        $data['js'] = preg_replace('/(\s\s+|\t|\n)/', '', "$('#inputProductName').val('{$data['name']}');
            $('#inputProductCode').val('{$data['code']}');
            $('#inputProductPrice').val('{$data['price']}');
            $('.for-dv select').val('Single').change();
            $('.weight').eq(0).val('{$data['material']}');
            $('.weight').eq(1).val('{$data['size']}');
            $('.form-group-volume input').val('{$data['weight']}');            
            if ($('.for-orig select').length > 0) {
                $('.for-orig select').find('option[text=\"Việt Nam\"]').attr('selected', true);
                $('.for-dv select').val('Single').change();
            }
            $('#inputProductName').trigger('change');
            $('#inputProductCode').trigger('change');
            $('#inputProductPrice').trigger('keyup');
            $('#inputProductPrice').trigger('blur');
            $('.for-dv select').trigger('change');
            $('.weight').eq(0).trigger('change');
            $('.weight').eq(1).trigger('change');    
            
            $('.boxSEO .form-control').eq(0).val('{$data['name']}');
            $('.boxSEO .form-control').eq(2).val('Mua {$data['name']} chính hãng giá tốt tại vuongquocbalo.com, sendo.vn, lazada.vn, giao hàng toàn quốc');
            $('.boxSEO .form-control').eq(0).trigger('change');
            $('.boxSEO .form-control').eq(1).trigger('change');            
            
            $('.form-group-volume input').trigger('blur');
            tinyMCE.execCommand('fontSize', false, '12pt');
            tinyMCE.activeEditor.setContent('<div style=\"font-size:13px;\">{$data['content']}</div><br><br>');
            $('html, body').animate({ scrollTop: 200}, 100);");
            return $this->getViewModel(array(
                'data' => $data
            )
        );
    }
        
    /**
     * Copy Product
     *
     * @return Zend\View\Model
     */
    public function copyproduct1Action()
    {
        include_once getcwd() . '/include/simple_html_dom.php';
        $request = $this->getRequest();  
        $param = $this->getParams(array(                      
            'product_id' => 0,            
            'category_id' => 0,            
        ));
        $id = $param['product_id'];
        $data = Products::getDetail($id, 1);
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        $data['tags'] = [];
        if (!empty($data['categories'])) {            
            foreach ($data['categories'] as $category) {                
                if ($data['website_id'] == 1) {      
                    if ($category['category_id'] == 16) {          
                        $category['name'] = 'Túi rút';
                    }
                    $category['name'] = str_replace('Ba Lô', 'balo', $category['name']);
                }
                $data['tags'][] = $category['name'];
            }            
        }
        $size = '';
        if (!empty($data['attributes'])) {            
            foreach ($data['attributes'] as $attribute) {
                if (in_array($attribute['field_id'], [7,9]) && empty($size)) {                                    
                    $size = $attribute['value'];
                }
                if ($attribute['field_id'] == 1) {
                    $data['tags'][] = $attribute['name'] . ' ' . $attribute['value'];
                }
            }
        }
        $data['tags'] = strtolower(implode(',', $data['tags'])); 
        /*
        if (!empty($data['more']) && $data['website_id'] == 1) {            
            $html = str_get_html($data['more']);
            foreach($html->find('p') as $element) {
                if (strpos(strtolower($element->innertext), 'cm') !== false) {
                    $data['size'] = $element->innertext;
                    $data['size'] = str_replace("&nbsp;", " ", $data['size']);
                    $data['size'] = preg_replace("/\s+/", " ", $data['size']);
                    $data['size'] = str_replace('Kích thước:', '', $data['size']);
                    $data['size'] = str_replace('Kích thước', '', $data['size']);                    
                    $data['size'] = trim(html_entity_decode($data['size']), " \t\n\r\0\x0B\xC2\xA0");
                    $data['size'] = trim($data['size']);
                    if ($data['size'] != $size) {
                        if (Api::call('url_products_updatesizeattr', [
                            'category_id' => $param['category_id'], 
                            'product_id' => $id, 
                            'field_id' => $data['website_id'] == 1 ? 7 : 9, 
                            'value' => $data['size']])) {
                            Products::removeCache($id);   
                            $data = Products::getDetail($id);
                        }
                    }
                    break;
                }
            }
        }
        * 
        */
        $data['name'] = str_replace(['&'], ['-'], $data['name']);        
        if (array_intersect([15], $data['category_id'])) {   
            if (empty($param['small_size'])) {
                $data['price'] = '220000';                              
                $data['content'] = implode(PHP_EOL, [                
                        '<strong>THÔNG TIN SẢN PHẨM:</strong>',                    
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',                        
                        '- Có 2 ngăn để vừa laptop 14", có chổ để bình nước.',               
                        '- Phù hợp đựng tập vở cho học sinh cấp 1, cấp 2, đựng đồ đi chơi.',
                        '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                    ]
                );
            } else {
                $data['price'] = '199000';
                $data['content'] = implode(PHP_EOL, [     
                        '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                        '- Balo và túi chéo giả da độc đáo, kích thước ngang 26 x cao 32 x rộng 9 (cm).',                       
                        '- Dây đeo tháo rời, thiết kế 2 trong 1 tiện lợi, vừa có thể đeo vai như balo vừa có thể đeo chéo.',
                        '- Có 1 ngăn lớn và ngăn đựng vừa Laptop 12"/Ipad',
                        '- Phù hợp đựng tập vở, tài liệu, giấy A4, Laptop 12"/Ipad đi học thêm, đi làm, đi chơi, đựng đồ cho bé đi nhà trẻ, mẫu giáo',
                        '- Balo giả da simili hàng Việt Nam xuất khẩu, chất lượng đảm bảo không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',                                       
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng',                                          
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',
                    ]
                );
            }
            $data['material'] = 'Simili';
        } elseif (array_intersect([16], $data['category_id'])) {
            $data['price'] = '99000';
            $data['content'] = implode(PHP_EOL, [ 
                    '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                    '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',                      
                    '- Phù hợp đựng tập vở đi học thêm hoặc đựng đồ đi chơi.',                               
                    '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                    '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                ]
            );
        } elseif (array_intersect([99], $data['category_id'])) {
            $data['content'] = implode(PHP_EOL, [    
                    '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                    '- <strong>Túi chéo mini xinh xắn, Kích thước ngang 24 x 17 (cm)</strong>.',    
                    '- Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm, các vật dụng cá nhân cho nữ, ... ',    
                    '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                    '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                ]
            );                
         } elseif (array_intersect([8], $data['category_id'])) {
            $data['price'] = '185000';
            $data['content'] = strip_tags($data['content'], '<b><strong><div><span><br><ul><li>');
            $data['content'] = implode(PHP_EOL, [    
                    '<strong>THÔNG TIN SẢN PHẨM:</strong>',
                    '- <strong>Túi chéo nhỏ xinh xắn, kích thước ngang 34 x cao 25 x rộng 9 (cm)</strong>.',    
                    '- Sử dụng đựng tập vở, tài liệu, giấy A4, máy tính bảng.',    
                    '- Có 1 ngăn lớn, phù hợp đi học thêm, đi làm, đi chơi.',    
                    '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                    '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                ]
            );            
        } elseif (array_intersect([11], $data['category_id'])) {
            $data['price'] = '150000';
            $data['content'] = strip_tags($data['content'], '<b><strong><div><span><br><ul><li>');
            $data['content'] = implode(PHP_EOL, [
                "<p>- Chiếc gối là một vật dụng thân thiết gắn liền với cuộc sống hằng ngày của chúng ta. Ngày nay kiểu dáng của những chiếc gối đã không còn nằm trong khuôn khổ cũ. Mẫu mã được thanh đổi liên tục và phù hợp với trào lưu. Gối in hình vừa có thể dùng vừa làm quà tặng tới bạn bè, người thân đều được!</p>",
                "<p>- Giấc ngủ của bạn sẽ trở nên êm đềm hơn khi trên chiếc gối in hình ảnh mà bạn yêu thích. Hình những con vật dễ thương, những nhân vật hoạt hình mà bạn yêu thích, ca sĩ nhóm nhạc mà bạn thần tượng….. tất cả đều có thể in lên chiếc gối quen thuộc của bạn.</p>",
                "",
                "<p>- <strong>Kích thước gối ngang 38 x cao 7 x rộng 38 (cm)</strong></p>",
                "<p>- Chất liệu vải nhung mịn, ruột gối bên trong là gòn bi không xẹp, in hình độc đáo</p>",             
                "<p>- Công nghệ in Nhật Bản, bền màu trong thời gian dài sử dụng.</p>",
            ]);
            $data['size'] = 'Ngang 38 x cao 7 x rộng 38 (cm)';
            $data['weight'] = '500';
            $data['material'] = 'Vải nhung';
        } else {
            if (empty($data['price_src'])) {
                $data['price_src'] = $data['original_price'];
            }
            //$data['price'] = $data['price_src'] + round((20/100)*$data['price_src'], -3);
            $data['price'] = $data['price_src'];
            $data['price'] = substr_replace($data['price'], '9', -4, -3);
            $data['content'] = strip_tags($data['content'], '<p><b><strong><div><span><br><ul><li>');
        }
        if (!empty($data['attributes'])) {       
            foreach ($data['attributes'] as &$attribute) {
                switch ($attribute['field_id']) {
                    case 15: // Trọng lượng
                        if (!empty($param['small_size'])) {
                            $attribute['value'] = 400;
                        }
                        $data['weight'] = $attribute['value'];
                        break;
                    case 7: // Kích thước (D x R x C)
                        if (!empty($param['small_size'])) {
                            $attribute['value'] = '26x9x32';
                        }
                        $data['size'] = $attribute['value'];
                        break;
                    case 3: // Chất liệu
                        $data['material'] = $attribute['value'];
                        break;
                }
            }
            if (!empty($data['attributes']) && !array_intersect([15, 16, 8, 11, 99], $data['category_id'])) {  
                $data['content'] = '';
                $data['content'] .= '<strong>THÔNG TIN SẢN PHẨM:</strong>' . PHP_EOL;
                $data['content'] .= '<ul>';               
                foreach ($data['attributes'] as $attribute) {
                    if (!empty($attribute['value'])) {
                        $data['content'] .= '<li>' . $attribute['name'] . ': ' . $attribute['value'] . '</li>'; 
                    }
                }
                $data['content'] .= '</ul>';
            } else {
                $renamed = false;
                $hasMadeIn = false;
                foreach ($data['attributes'] as $attribute) {
                    if ($renamed == false && array_intersect([15], $data['category_id']) && $attribute['field_id'] == 1) {
                        if (empty($param['small_size'])) {                            
                            $data['name'] = str_replace(['BL '], ['Balo học sinh ' . ($attribute['value']) . ' '], $data['name']);
                        } else {
                            $newCode = $data['code'] . '-S';  
                            $data['code_copy'] = $data['code'];
                            $data['name'] = str_replace(['BL '], ['Balo Và Túi đeo chéo 2 trong 1 ' . ($attribute['value']) . ' '], $data['name']);
                            $data['code'] = $newCode;                            
                        }
                        $renamed = true;
                    } elseif ($renamed == false && array_intersect([16], $data['category_id']) && $attribute['field_id'] == 1) {
                        $data['name'] = str_replace(['Túi rút '], ['Balo dây rút - Túi rút ' . strtolower($attribute['value']) . ' '], $data['name']);
                        if (strlen($data['name']) > 70) {
                            //$data['name'] = 'Balo dây rút - Túi rút ' .  strtolower($attribute['value']) . ' - ' . $data['code'];
                        }
                        $renamed = true;
                    } elseif ($renamed == false && array_intersect([8], $data['category_id']) && $attribute['field_id'] == 1) {
                        $data['name'] = str_replace(['Túi chéo '], ['Túi chéo ' . strtolower($attribute['value']) . ' '], $data['name']);
                        if (strlen($data['name']) > 70) {
                            //$data['name'] = 'Balo dây rút - Túi rút ' .  strtolower($attribute['value']) . ' - ' . $data['code'];
                        }
                        $renamed = true;
                    }
                    if ($attribute['field_id'] == 14 && $hasMadeIn == true) {
                        continue;
                    }
                    if ($attribute['field_id'] == 14) {
                        $hasMadeIn = true;
                    }
                    if (!empty($attribute['value'])) {                        
                        $data['content'] .= PHP_EOL . '- ' . $attribute['name'] . ': ' . $attribute['value']; 
                    }
                }
            }
        }      
        if (!empty($data['more'])) {              
            $data['more'] = preg_replace('#(<br */?>\s*)+#i', '<br>', $data['more']); 
            $data['more'] = strip_tags($data['more'], '<center><div><strong><br><p><b>');
            $data['content'] .= PHP_EOL . '<strong>GIỚI THIỆU SẢN PHẨM ' . mb_strtoupper($data['name']) . ':</strong>' . PHP_EOL . $data['more'] . PHP_EOL;
        }
        
        /*
        $data['content'] = preg_replace('/(\s\s+|\t)/', ' ',$data['content']);
        $data['content'] = str_replace(['<br>','<p>','<p style="text-align: justify;">','<li style="text-align: justify;">','</p>'], [PHP_EOL,PHP_EOL,PHP_EOL,PHP_EOL,''], $data['content']);            
        if (array_intersect([1,2,3,4,5,6], $data['category_id'])) { 
            $data['weight'] = 400;
            $data['seo'] = 'túi xách, cặp táp nam nữ, túi chéo, túi đeo chéo, túi chéo sinh viên, balo nam nữ, bóp ví nam nữ';           
        } elseif (array_intersect([20,21], $data['category_id'])) {
            $data['weight'] = 150;
            $data['seo'] = 'túi xách, cặp táp nam nữ, túi chéo, túi đeo chéo, túi chéo sinh viên, balo nam nữ, bóp ví nam nữ';           
        } elseif (array_intersect([69,70], $data['category_id'])) {
            $data['weight'] = 300;
            $data['seo'] = 'đầm suông, đầm xòe, đầm maxi, đầm ôm, đầm dạ hội, đầm xếp ly, đầm denim, đầm trễ vai, đầm hai dây';           
        } */ 
        $data['name'] = str_replace("'", '', $data['name']);
        $data['name'] = preg_replace('/(\s\s+|\t|\n)/', ' ', $data['name']);
        $data['content'] = str_replace(
            [
                'vuongquocbalo.com', 
                'thoitrang1.net',
                '<p></p>'
            ], 
            [
                '<strong><a href="https://www.sendo.vn/shop/vuongquocbalo/">vuongquocbalo.com</a></strong>', 
                '<strong><a href="https://www.sendo.vn/shop/vuongquocbalo/">vuongquocbalo.com</a></strong>', 
                ''
            ], 
            $data['content']
        );
        $data['policy'] = implode('<br/>', [
            "<strong style=\"color:red\">TIÊU CHÍ BÁN HÀNG CỦA VUONGQUOCBALO:</strong>",
            "- Không bán sản phẩm kém chất lượng.",
            "- Giá cả cạnh tranh nhất, quý khách nên so sánh giá sản phẩm trước khi quyết định đặt hàng.",
            "- Cam kết hàng giống hình.",
            "- Sản phẩm mô tả đầy đủ màu sắc, kích thước, chất liệu, quý khách vui lòng xem mô tả sản phẩm.",
        ]);
        if (array_intersect([15, 16, 8, 11, 99], $data['category_id'])) {  
            $data['content'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['content']); 
        } else {           
            $data['content'] = str_replace(["’","'"], ['',''], $data['content']); 
        }
        
        
        $data['content'] = $data['policy'] . '<br/><br/><br/><strong>HÌNH ẢNH SẢN PHẨM:</strong><br/><br/>' . $data['content'] . '<br/>';
        
        //$data['content_html'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['content']);
        $data['content_html'] = app_compress_content($data['content']);
        if (empty($data['code_copy'])) {
            $data['code_copy'] = $data['code'];
        }        
        $data['name'] = htmlspecialchars($data['name']);
        $data['content_html'] = htmlspecialchars($data['content_html']);
        $data['js'] = preg_replace('/(\s\s+|\t|\n)/', '', "$('#inputProductName').val('{$data['name']}');
            $('#inputProductCode').val('{$data['code']}');
            $('#inputProductPrice').val('{$data['price']}');
            $('.for-dv select').val('Single').change();
            $('.weight').eq(0).val('{$data['material']}');
            $('.weight').eq(1).val('{$data['size']}');
            $('.form-group-volume input').val('{$data['weight']}');            
            if ($('.for-orig select').length > 0) {
                $('.for-orig select').find('option[text=\"Việt Nam\"]').attr('selected', true);
                $('.for-dv select').val('Single').change();
            }
            $('#inputProductName').trigger('change');
            $('#inputProductCode').trigger('change');
            $('#inputProductPrice').trigger('keyup');
            $('#inputProductPrice').trigger('blur');
            $('.for-dv select').trigger('change');
            $('.weight').eq(0).trigger('change');
            $('.weight').eq(1).trigger('change');    
            
            $('.boxSEO .form-control').eq(0).val('{$data['name']}');
            $('.boxSEO .form-control').eq(2).val('Mua {$data['name']} chính hãng giá tốt tại vuongquocbalo.com, sendo.vn, lazada.vn, giao hàng toàn quốc');
            $('.boxSEO .form-control').eq(0).trigger('change');
            $('.boxSEO .form-control').eq(1).trigger('change');            
            
            $('.form-group-volume input').trigger('blur');
            tinyMCE.execCommand('fontSize', false, '12pt');
            tinyMCE.activeEditor.setContent('<div style=\"font-size:13px;\">{$data['content_html']}</div><br><br>');
            $('html, body').animate({ scrollTop: 200}, 100);");
            return $this->getViewModel(array(
                'data' => $data
            )
        );  
    }
    
    /**
     * Ajax share to facecbook group
     *
     * @return Zend\View\Model
     */
    public function sdshareAction()
    {   
        set_time_limit(0);
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
            $product = Products::getDetail($param['product_id'], 1);
            if (empty($product)) {
                exit;
            }            
            if (empty($product['image_facebook'])) {
                Api::call('url_products_updatefbimage', [
                    'products' => \Zend\Json\Encoder::encode([
                        [
                            'website_id' => $product['website_id'], 
                            'product_id' => $product['product_id'], 
                            'url_image' => $product['url_image'],
                            'name' => $product['name'],
                        ]
                    ])
                ]);
                $product = Products::getDetail($product['product_id'], 1);
            }           
            if (empty($product['url_other'])) {                
                $result = array(
                    'status' => 'OK',
                    'message' => 'Not exist on sendo.vn',
                );
                die(\Zend\Json\Encoder::encode($result));                
            }               
            $caption = 'Shop Balo Học Sinh, Balo Teen';
            if (array_intersect([15], $product['category_id'])) {                 
                if (!empty($product['url_sendo1'])) {
                    $code = 'S' . $product['code_src'];
                    $product['price'] = '159000';
                    $product['name'] = str_replace([$product['code'], 'BL ',], [$code, 'Balo Ipad - Học thêm - Đi chơi '], $product['name']);
                    $product['code'] = $code;
                } else {
                    $product['price'] = '187000';
                    $product['name'] = str_replace(['BL '], ['Balo Teen - Học sinh - Laptop '], $product['name']);
                }              
            } elseif (array_intersect([16], $product['category_id'])) {
                $product['price'] = '69000';
            } else {
                $product['price'] = $product['price'] - round((5/100)*$product['price'], -3);
                $caption = 'Zanado Shop';
            }            
            $product['original_price'] = 0;
            $track = 'utm_source=facebook&utm_medium=social&utm_campaign=product_sendo';
            if ($product['website_id'] == 1) {        
                $product['url'] = "http://vuongquocbalo.com/sendo?{$track}&id={$product['product_id']}&u={$product['url_other']}";                
            } else {
                $product['url'] = "http://thoitrang1.net/sendo?{$track}&id={$product['product_id']}&u={$product['url_other']}";        
            }    
            $product['short_url'] = Util::googleShortUrl($product['url']); 
            $data = app_get_fb_share_content($product, $caption);
         
            /*
            $pageId = '306098189741513';	
            $id = Fb::postToPage($pageId, $data, $AppUI->fb_access_token, $errorMessage);
            if (!empty($id)) {
                $result[] = "Page:{$pageId} - Post:{$id}";             
            } else {
                $result[] = "Page:{$pageId}: - Post:{$errorMessage}"; 
            }
            $result = array(
                'status' => 'OK',
                'message' => implode('<br/>', $result),
            );
            die(\Zend\Json\Encoder::encode($result));
            */
            
            $result = [];           
            $groupIds = Arr::rand(app_facebook_groups(), 4);
            //$groupIds = app_facebook_groups();            
            foreach ($groupIds as $groupId) {                
                if ($groupId == '378628615584963' && !in_array($AppUI->facebook_id, ['103432203421638'])) {
                    continue;
                }
                $id = Fb::postToGroup($groupId, $data, $AppUI->fb_access_token, $errorMessage);
                if (!empty($id)) {
                   $result[] = "Group:{$groupId} - Post:{$id}";             
                } else {
                    $result[] = "Group:{$groupId}: - Post:{$errorMessage}"; 
                }
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
     * Ajax remove a product from category
     *
     * @return Zend\View\Model
     */
    public function shareplusAction()
    { 
        if (!$this->isAdmin()) {
            exit;
        }
        $AppUI = $this->getLoginInfo();
        $request = $this->getRequest();    
        $param = $this->getParams();
        if ($request->isXmlHttpRequest() 
            && $request->isPost() 
            && !empty($param['product_id'])) {      
            $product = Products::getDetail($param['product_id']);
            if (empty($product)) {
                exit;
            }
            if (empty($AppUI->google_access_token) || strtotime($AppUI->access_token_expires_at) < time()) {
                $result = array(
                    'status' => 'OK',
                    'message' => 'Invalid user or Token have been expired',
                );
                die(\Zend\Json\Encoder::encode($result));   
            }
            $data['url'] = $param['url'];
            $data['name'] = $product['name'];
            $result = GooglePlus::post($data, $accessToken);
            $result = array(
                'status' => 'OK',
                'message' => implode('<br/>', $result),
            );
            /*
            if ($product['website_id'] == 1) {
                $siteUrl = 'http://vuongquocbalo.com';                
                $product['url'] = $siteUrl . '/' . name_2_url($product['name']) . '?utm_source=blogger&utm_medium=social&utm_campaign=product';
				$product['content'] = strip_tags($product['content'], '<p><div><span><ul><li><strong><b><br><center>');
				if (!empty($product['images'])) {
					foreach ($product['images'] as $image) { 
						$image['url_image'] = str_replace('.dev', '.com', $image['url_image']);
						$product['content'] .= "<center><p><img style=\"width:80%\" src=\"{$image['url_image']}\"/></p></center>";
					}    
				}
			} else {
                $siteUrl = 'http://thoitrang1.net';                
                $product['url'] = $siteUrl . '/' . name_2_url($product['name']) . '?utm_source=blogger&utm_medium=social&utm_campaign=product';
				$product['content'] = strip_tags($product['content'], '<p><div><span><ul><li><strong><b><br><center>');
                if (!empty($product['images'])) {
					foreach ($product['images'] as $image) { 
						$image['url_image'] = str_replace('.vn', '.net', $image['url_image']);
						$product['content'] .= "<center><p><img style=\"width:80%\" src=\"{$image['url_image']}\"/></p></center>";
					}    
				}
            }
            $blogs = [];
            $labels = [];
            if (!empty($product['categories'])) {
                foreach ($product['categories'] as $category) {
                    $labels[] = $category['name'];
                    $blogId = app_bloggers($category['category_id']);
					if (!empty($blogId)) {
						$blogs = array_merge($blogs, $blogId);
					}
                }
            }            
            if (array_intersect([15, 16], $product['category_id'])) {
                $data['content'] = implode('<br/>', [                
                        $product['content'],
                        "<center style=\"width:100%;color:#D4232B;font-size:30px;padding:5px;\"> Giá: " . app_money_format($product['price']) . '</center>',               
                        "<center><p><a href=\"{$product['url']}\"><img src=\"{$siteUrl}/web/images/buy_now.gif\"/></a></p></center>",		
                    ]
                );
                if (!empty($product['attributes'])) {
                    foreach ($product['attributes'] as $attribute) {
                        if (!empty($attribute['value'])) {
                            $labels[] = $attribute['value'];
                        }
                    } 
                }
            } else {
                $data['content'] = implode('<br/>', [                
                        $product['short'],
                        $product['content'],
                        "<center style=\"width:100%;color:#D4232B;font-size:30px;padding:5px;\"> Giá: " . app_money_format($product['price']) . '</center>',               
                        "<center><p><a href=\"{$product['url']}\"><img src=\"http://vuongquocbalo.com/web/images/buy_now.gif\"/></a></p></center>",		
                    ]
                );
            }
            $data['labels'] = $labels;
            $result = [];
			if (!empty($blogs)) {
				$blogs = array_unique($blogs);
				foreach ($blogs as $blogId) {
                    $check = Api::call('url_bloggerpostids_all', [
                        'product_id' => $product['product_id'],
                        'blog_id' => $blogId                
                    ]);
                    if (empty($check)) {
                        $postId = Blogger::post($blogId, $data, $AppUI->google_access_token, $errorMessage);
                        if (!empty($postId)) {
                            $result[$blogId] = $postId;
                            Api::call('url_bloggerpostids_add', [
                                'product_id' => $product['product_id'],
                                'blog_id' => $blogId,
                                'post_id' => $postId,                
                            ]);
                        } else {
                            $result[$blogId] = $errorMessage;
                        }
                    } else {
                        $result[$blogId] = 'Posted';
                    }
				}
				$result = array(
					'status' => 'OK',
					'message' => implode('<br/>', $result),
				);
			} else {
				$result = array(
					'status' => 'OK',
					'message' => 'Not found Blog ID',
				);
			}
             * 
             */
            die(\Zend\Json\Encoder::encode($result));       
        }
        exit;
    }
    
}
