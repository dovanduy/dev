<?php
/*
   php /home/vuong761/public_html/shell/vuongquocbalo/facebook_keyword.php
 * Balo 
 */
include ('base.php');
$categories = [2, 3, 5, 6, 15, 16, 99, 100];
$limit = 40;
$limitImagePerProduct = 1;
$authFilename = 'facebook_login5.serialize';
if (!file_exists($authFilename)) { 
    batch_info('Token does not exists');
    exit;
}
/************************************/
batch_info('BEGIN');        
$AppUI = unserialize(app_file_get_contents($authFilename));     
$shareds = call('/facebookwallshares/all', [
        'user_id' => $AppUI->user_id,                           
    ]
);
$countPosted = 0;
foreach ($searchs as $search) {  
    if ($countPosted > 0) {
        break;
    }
    $poasted = false;
    foreach ($shareds as $shared) {  
        if ($shared['keyword'] == $search['keyword']) {
            batch_info("The keyword {$search['keyword']} have been already shared");
            $poasted = true;
            break;
        }
    }
    if (!empty($search['disable']) || $poasted == true) {
        continue;
    }
    //sleep(rand(10*60, 45*60));
    if (empty($search['link'])) {
        $search['link'] = "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q={$search['keyword']}";
    }
    if (!empty($search['category_id'])) {
        $categories = (array) $search['category_id'];
    }
    $link = app_short_url($search['link']);
    $param = [
        'category_id' => implode(',', $categories),
        'limit' => $limit,
        'keyword' => !empty($search['keyword']) ? $search['keyword'] : '',
        'brand_id' => !empty($search['brand_id']) ? $search['brand_id'] : 0,
        'no_create_image_facebook' => 1,
        'option_id' => !empty($search['option_id']) ? $search['option_id'] : '',
        'best_sell' => !empty($search['best_sell']) ? $search['best_sell'] : 0,
    ];   
    $productIds = [];
    $bestSellFile = 'bast_sell.serialize';
    if (file_exists($bestSellFile) && !empty($param['best_sell'])) {
        $productIds = unserialize(app_file_get_contents($bestSellFile));
        $param['not_in_product_id'] = implode(',', $productIds);
    }
    $products = call('/products/allforsendo', $param);
    if (empty($products)) {
        batch_info('Product not found');
        exit;
    }    
    $i = 0;    
    $data = [
        'message' => str_replace('{link}', $link, implode(PHP_EOL, $search['message'])),              
        'caption' => 'Balo Học Sinh, Balo Teen'
    ];
    if (!empty($param['best_sell'])) {
        $productIds = array_merge($productIds, app_array_field($products, 'product_id'));      
        app_file_put_contents($bestSellFile, serialize($productIds));
    }
    foreach ($products as $product) {
        if (empty($product['url_image'])) {
            continue;
        }
        $product['category_id'] = explode(',', $product['category_id']);
        if (array_intersect([15, 16, 99], $product['category_id'])) {
            if (array_intersect([15], $product['category_id'])) { 
                if (!empty($product['url_sendo1'])) {  
                    $code = 'S' . $product['code_src'];
                    $product['price'] = '159.000';
                    $product['name'] = str_replace([$product['code'], 'BL ',], [$code, 'Balo Ipad - Học thêm - Đi chơi '], $product['name']);
                    $product['code'] = $code;
                } else {
                    $product['price'] = '187.000';
                    $product['name'] = str_replace(['BL '], ['Balo Teen - Học sinh - Laptop '], $product['name']);
                }               
            } elseif (array_intersect([16], $product['category_id'])) {
                $product['price'] = '59.000';
            } elseif (array_intersect([99], $product['category_id'])) {
                $product['price'] = '70.000';
            }
            $photoData = [
                'caption' => implode(PHP_EOL, [ 
                    "💼 {$product['name']}",
                    "💰 {$product['price']} VNĐ",   
                    "Mã hàng: {$product['code']}",           
                    "Xem chi tiết {$link}"
                ]),
                'url' => $product['url_image'],
            ];
            $photoId = uploadUnpublishedPhoto($photoData, $AppUI->fb_access_token, $errorMessage);
            if (!empty($photoId)) {   
                $data["attached_media[{$i}]"] = '{"media_fbid":"' . $photoId . '"}';
                $i++;
            }
        } else {
            //$data['caption'] = 'Zanado Shop';                
            $product = call(
                '/products/detail', 
                array(                    
                    'product_id' => $product['product_id'],
                    'get_images' => 1
                )
            );          
            if (empty($search['price'])) { 
                $product['price'] = $product['price'] - round((5/100)*$product['price'], -3);                           
            } else {
                $product['price'] = $search['price'];
            }
            $product['price'] = app_money_format($product['price']); 
            if (!empty($product['images'])) {
                $photos = array();
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
                if (!empty($photos)) { 
                    if (count($products) <= 10) {
                        $limitImagePerProduct = 4;
                    } elseif (count($products) <= 5) {
                        $limitImagePerProduct = 8;
                    }
                    $photos = array_slice($photos, 0, $limitImagePerProduct);
                    foreach ($photos as $imageUrl => $name) { 
                        $photoData = [
                            'caption' => implode(PHP_EOL, [ 
                                "💼 {$product['name']}",
                                "💰 {$product['price']}",   
                                "Mã hàng: {$product['code']}",           
                                "Xem chi tiết {$link}"
                            ]),
                            'url' => $imageUrl,
                        ];
                        $photoId = uploadUnpublishedPhoto($photoData, $AppUI->fb_access_token, $errorMessage);
                        if (!empty($photoId)) {                               
                            $data["attached_media[{$i}]"] = '{"media_fbid":"' . $photoId . '"}';
                            $i++;
                        }
                    }
                }
            }
        }            
    }   
    $postId = postToWall($data, $AppUI->fb_access_token, $errorMessage);
    if (!empty($postId)) {
        batch_info("Post OK: {$postId}");
        call('/facebookwallshares/add', 
            [
                'user_id' => $AppUI->user_id,           
                'facebook_id' => $AppUI->facebook_id,           
                'keyword' => $search['keyword'],
                'social_id' => $postId,
            ]
        );
        $countPosted++;
    } else {
        batch_info("Post FAIL: {$errorMessage}");
    }    
}
batch_info('END');
exit;