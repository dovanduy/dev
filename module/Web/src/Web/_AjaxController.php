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
    
    /**
     * Copy Product
     *
     * @return Zend\View\Model
     */
    public function copyproductAction()
    {
        include_once getcwd() . '/include/simple_html_dom.php';
        $request = $this->getRequest();  
        $param = $this->getParams(array(                      
            'product_id' => 0,            
            'category_id' => 0,            
        ));
        $id = $param['product_id'];
        $data = Products::getDetail($id);
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
                $data['price'] = '187000';
                $data['content'] = implode(PHP_EOL, [                
                        '- Kích thước ngang 32 x cao 41.5 x rộng 14.5 (cm), có 2 ngăn để vừa laptop 14", có chổ để bình nước',
                        '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng'
                    ]
                );
                $data['size'] = 'Ngang 32 x cao 41.5 x rộng 14.5 (cm)';
                $data['weight'] = '550';
            } else {
                $data['price'] = '159000';
                $data['content'] = implode(PHP_EOL, [                                        
                        '- Balo nhỏ xinh xắn, kích thước ngang 26 x cao 32 x rộng 9 (cm)',
                        '- Sử dụng để đựng tập vở, tài liệu, giấy A4, Laptop 12"/Ipad',
                        '- Có 1 ngăn lớn và ngăn đựng vừa Laptop 12"/Ipad. Phù hợp đi học thêm, đi làm, đi chơi.',
                        '- Dây đeo tháo rời',
                        '- Balo giả da simili hàng Việt Nam xuất khẩu, chất lượng đảm bảo không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',                                       
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng'
                    ]
                );
                $data['size'] = 'Ngang 26 x cao 32 x rộng 9 (cm)';
                $data['weight'] = '380';                
            }
            $data['material'] = 'Simili';
        } elseif (array_intersect([16], $data['category_id'])) {
            $data['price'] = '69000';
            $data['content'] = implode(PHP_EOL, [                
                    '- Kích thước 29 x 40 (cm)',
                    '- Ba lô dây rút hàng Việt Nam xuất khẩu, chất lượng đảm bảo',               
                    '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn',               
                    '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng'
                ]
            );
            $data['size'] = '29 x 40 (cm)';
            $data['weight'] = '250';
            $data['material'] = 'Simili';
        } else {
            $data['price'] = $data['price'] - round((5/100)*$data['price'], -3);
            $data['content'] = strip_tags($data['content'], '<p><b><strong><div><span><br><ul><li>');
        }
        if (!empty($data['attributes'])) {         
            if (!empty($data['attributes']) && !array_intersect([15, 16], $data['category_id'])) {  
                if (!empty(strip_tags($data['content']))) {
                    $data['content'] = '<strong>TÓM TẮC SẢN PHẨM:</strong><br>' . $data['content'];
                    $data['content'] .= PHP_EOL;
                } else {
                    $data['content'] = '';
                }
                $data['content'] = '';
                $data['content'] .= '<strong>THÔNG TIN SẢN PHẨM:</strong>' . PHP_EOL;
                $data['content'] .= '<ul>';               
                foreach ($data['attributes'] as $attribute) {
                    if (!empty($attribute['value'])) {
                        $data['content'] .= '<li>' . $attribute['name'] . ': ' . $attribute['value'] . '</li>'; 
                    }
                    if ($attribute['name'] == 'Kích Thước') {
                        $data['size'] = $attribute['value'];
                    }
                    if ($attribute['name'] == 'Chất liệu') {
                        $data['material'] = $attribute['value'];
                    }
                }
                $data['content'] .= '</ul>';
            } else {
                $renamed = false;
                foreach ($data['attributes'] as $attribute) {
                    if ($renamed == false && array_intersect([15], $data['category_id']) && $attribute['field_id'] == 1) {
                        if (empty($param['small_size'])) {                           
                            $data['name'] = str_replace(['BL '], ['Balo Teen - Học sinh - Laptop '], $data['name']);
                        } else {
                            $newCode = 'S' . $data['code_src'];  
                            $data['code_copy'] = $data['code'];
                            $data['name'] = str_replace([$data['code'], 'BL ',], [$newCode, 'Balo Ipad - Học thêm - Đi chơi '], $data['name']);
                            $data['code'] = $newCode;                            
                        }
                        $renamed = true;
                    } elseif ($renamed == false && array_intersect([16], $data['category_id']) && $attribute['field_id'] == 1) {
                        $data['name'] = str_replace(['Túi rút '], ['Balo dây rút - Túi rút ' . strtolower($attribute['value']) . ' '], $data['name']);
                        if (strlen($data['name']) > 70) {
                            $data['name'] = 'Balo dây rút - Túi rút ' .  strtolower($attribute['value']) . ' - ' . $data['code'];
                        }
                        $renamed = true;
                    }
                    if (!empty($attribute['value'])) {
                        $data['content'] .= PHP_EOL . '- ' . $attribute['name'] . ': ' . $attribute['value']; 
                    }
                }
            }
        }      
        if (!empty($data['more'])) {  
            $data['weight'] = '700';
            if (array_intersect([51, 75, 76, 77], $data['category_id'])) {
                $data['weight'] = '450';
            } elseif (array_intersect([20, 21], $data['category_id'])) {
                $data['weight'] = '200';
            } elseif (array_intersect([66], $data['category_id'])) {
                $data['weight'] = '450';
            } elseif (array_intersect([65], $data['category_id'])) {
                $data['weight'] = '400';     
            } elseif (array_intersect([70], $data['category_id'])) {
                $data['weight'] = '450';
            } elseif (array_intersect([53, 78], $data['category_id'])) {
                $data['weight'] = '400';
            }
            $data['more'] = preg_replace('#(<br */?>\s*)+#i', '<br>', $data['more']); 
            $data['more'] = strip_tags($data['more'], '<br><p>');
            $data['content'] .= PHP_EOL . '<strong>MÔ TẢ CHI TIẾT SẢN PHẨM ' . mb_strtoupper($data['name']) . ':</strong>' . PHP_EOL . $data['more'] . PHP_EOL;
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
                '<strong>vuongquocbalo.com</strong>', 
                '<strong>thoitrang1.net</strong>',
                ''
            ], 
            $data['content']
        );
        $data['content_html'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['content']);
        if (empty($data['code_copy'])) {
            $data['code_copy'] = $data['code'];
        }
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
            $('.form-group-volume input').trigger('blur');
            tinyMCE.execCommand('fontSize', false, '12pt');
            tinyMCE.activeEditor.setContent('<div style=\"font-size:14px\">{$data['content_html']}</div><br><br>');
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
