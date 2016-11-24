<?php
/*
  php /home/vuong761/public_html/shell/vuongquocbalo/facebook.php 
 * 
 */
include ('base.php');
$auth = ['facebook_login.serialize', 'facebook_login2.serialize', 'facebook_login3.serialize', 'facebook_login4.serialize', 'facebook_login5.serialize'];
$auth = ['facebook_login.serialize', 'facebook_login2.serialize'];
$authFilename = $auth[array_rand($auth, 1)];
if (file_exists($authFilename)) {
    sleep(rand(1*60, 30*60));
    $limit = 1;
    $AppUI = unserialize(app_file_get_contents($authFilename));    
    $groups = app_array_rand(app_facebook_groups($AppUI->user_id), 8);
    foreach ($groups as $groupId) {        
        $products = call('/products/allforfacebook', [
                'category_id' => '8,15,16,99,100,101', 
                'group_id' => $groupId, 
                'limit' => $limit
            ]
        );
        $result = array();        
        foreach ($products as $item) {   
            $product = call(
                '/products/detail', 
                array(                    
                    'product_id' => $item['product_id'],
                    'get_images' => 1
                )
            );          
            if (empty($product) || empty($product['url_image'])) {
                batch_info('Not found product');
                exit;
            }
            $images = [];
            if (array_intersect([16], $product['category_id'])) {
                $images[] = $product['url_image'];
            } elseif (!empty($product['images'])) {                
                foreach ($product['images'] as $image) {                       
                    $images[] = $image['url_image'];
                }                
            }
            $product['price'] = app_money_format($product['price']);            
            $phongcachAttr = '';
            foreach ($product['attributes'] as $attribute) {            
                if ($attribute['field_id'] == 1 && !empty($attribute['value'])) {
                    $phongcachAttr = $attribute['value'];
                }                
            }
            if (array_intersect([15], $product['category_id'])) {   
                $product['short'] = implode(PHP_EOL, [                                                
                        'Phù hợp cho Teen, Học sinh cấp 1, cấp 2, cấp 3.',        
                        'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                        'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
                    ]
                );
                if (!empty($phongcachAttr)) {
                    $product['name'] = str_replace(['BL '], ['Balo Học sinh, Teen ' . $phongcachAttr . ' '], $product['name']);
                } 
                $images[] = 'http://img.vuongquocbalo.com/model/balo_lon_1.png';
                $images[] = 'http://img.vuongquocbalo.com/model/balo_lon_2.png';
                $images[] = 'http://img.vuongquocbalo.com/model/balo_lon_3.png';
                $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_1.png';
                $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_2.png';
                $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_3.png';
                $images[] = 'http://img.vuongquocbalo.com/model/balo_nho_4.png';                        
            } elseif (array_intersect([16], $product['category_id'])) {           
                $product['short'] = implode(PHP_EOL, [                      
                        'Phù hợp đựng tập vở đi học thêm hoặc đựng đồ đi chơi.', 
                        'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                        'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
                    ]
                );                           
                if (!empty($phongcachAttr)) {
                    $product['name'] = str_replace(['Túi rút '], ['Túi rút ' . $phongcachAttr . ' '], $product['name']);
                }           
                $images[] = 'http://img.vuongquocbalo.com/model/tui_rut_1.png';
                $images[] = 'http://img.vuongquocbalo.com/model/tui_rut_2.png';
                $images[] = 'http://img.vuongquocbalo.com/model/tui_rut_3.png';
            } elseif (array_intersect([99], $product['category_id'])) {
                $product['short'] = implode(PHP_EOL, [
                    'Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm cho nữ.',
                    'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                    'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
                        ]
                );
                $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_mini_1.png';
                $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_mini_2.png';
                $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_mini_3.png';
            } elseif (array_intersect([8], $product['category_id'])) {            
                $product['short'] = implode(PHP_EOL, [
                    'Phù hợp đựng tập vở, tài liệu, giấy A4, máy tính bảng.',
                    'Chất liệu simili 100% giả da, không thấm nước, không bong tróc, dễ lau chùi khi bị bẩn.',
                    'Hàng Việt Nam xuất khẩu, công nghệ in Nhật Bản cho hình in đẹp.',
                        ]
                );
                if (!empty($phongcachAttr)) {
                    $product['name'] = str_replace(['Túi chéo '], ['Túi chéo ' . $phongcachAttr . ' '], $product['name']);
                }
                $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_1.png';
                $images[] = 'http://img.vuongquocbalo.com/model/tui_cheo_2.png';
            }
            $message = [
                "💼 {$product['name']}",
                "💰 {$product['price']}",                               
                "Mã hàng: {$product['code']}",                
            ];            
            if (!empty($product['url_lazada'])) {
                $product['url'] = $product['url_lazada'];
            } elseif (!empty($product['url_lazada1'])) {
                $product['url'] = $product['url_lazada1'];
            } elseif (!empty($product['url_sendo1'])) {
                $product['url'] = $product['url_sendo1'];
            } elseif (!empty($product['url_sendo2'])) {
                $product['url'] = $product['url_sendo2'];
            } elseif (!empty($product['url_sendo3'])) {
                $product['url'] = $product['url_sendo3'];
            } elseif (!empty($product['url_sendo4'])) {
                $product['url'] = $product['url_sendo4'];
            } elseif (!empty($product['url_sendo5'])) {
                $product['url'] = $product['url_sendo5'];
            } elseif (!empty($product['url_sendo6'])) {
                $product['url'] = $product['url_sendo6'];
            } else {
                $product['url'] = "https://sendo.vn/shop/vuongquocbalo/";
            }
            $data = [
                'message' => implode(PHP_EOL, [
                    "💼 {$product['name']}",
                    "💰 {$product['price']}",                               
                    "Mã hàng: {$product['code']}",                    
                    "❝ {$product['short']} ❞",
                    "📞 098 65 60 943",
                    "✈ 🚏 🚕 🚄 Ship TOÀN QUỐC",                        
                    "Truy cập các website bên dưới để khám phá vuongquocbalo bạn nhé:",                        
                    "http://sendo.vn/shop/vuongquocbalo/",                            
                    "http://lazada.vn/vuong-quoc-balo/",                            
                    "http://vuongquocbalo.com/",                            
                ]),
                'link' => app_short_url($product['url']),        
                'picture' => $product['url_image'],        
                'caption' => 'vuongquocbalo.com'
            ];
            $postId = postToGroup($groupId, $data, $AppUI->fb_access_token, $errorMessage);
            if (!empty($postId)) {
                call('/productshares/add', [
                    'user_id' => $AppUI->id,
                    'facebook_id' => $AppUI->facebook_id,
                    'product_id' => $product['product_id'],
                    'group_id' => $groupId,
                    'social_id' => $postId
                ]);
                batch_info("Post OK {$groupId}: {$postId}");
            } else {
                batch_info("---Post FAIL {$groupId}: {$errorMessage}");
            }
        }
        sleep(rand(3*60, 8*60));     
    }
    batch_info('Done');
} else {
    batch_info('Token does not exists');
}
exit;