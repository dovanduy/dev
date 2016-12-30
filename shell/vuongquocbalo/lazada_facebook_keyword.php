<?php
/*
    php /home/vuong761/public_html/shell/vuongquocbalo/lazada_facebook_keyword.php
    php lazada_facebook_keyword.php
 */
include ('base.php');
$categories = [2, 3, 5, 6, 8, 15, 16, 99, 100, 101];
$limit = 40;
$limitImagePerProduct = 1;
$auth = ['facebook_login.serialize', 'facebook_login2.serialize', 'facebook_login5.serialize'];
$authFilename = $auth[array_rand($auth, 1)];
if (!file_exists($authFilename)) { 
    batch_info('Token does not exists');
    exit;
}
$searchs = [  
    [        
        'keyword' => "balo nam nữ",       
        'disable' => 0,        
        'price' => 159000,        
        'message' => [
            "Balo Nam Nữ Hàn quốc giảm giá trên Lazada",                        
            "Giá khuyến mãi chỉ 159.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "túi chéo nữ",       
        'disable' => 0,        
        'price' => 79000,        
        'category_id' => 99, 
        'message' => [
            "Túi chéo nữ simili giả da mini dễ thương đựng điện thoại, đồ trang điểm tiện dụng giảm giá trên Lazada",                        
            "Giá khuyến mãi chỉ 79.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "túi chéo",       
        'disable' => 0,        
        'price' => 119000,        
        'category_id' => 8,        
        'message' => [
            "Túi chéo simili giả da đựng tập vở, ipad tiện dụng giảm giá trên Lazada",                        
            "Giá khuyến mãi chỉ 119.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "balo, túi rút, túi chéo cpop",       
        'disable' => 0,        
        'price' => 59000,
        'option_id' => 5,
        'message' => [
            "Balo, Túi chéo, Túi rút phong cách Cpop giảm giá trên Lazada",                        
            "Giá khuyến mãi chỉ từ 59.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "balo, túi rút, túi chéo kpop",       
        'disable' => 0,        
        'price' => 59000,
        'option_id' => 18,
        'message' => [
            "Balo, Túi chéo, Túi rút phong cách Kpop giảm giá trên Lazada",                        
            "Giá khuyến mãi chỉ từ 59.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "balo, túi rút, túi chéo vpop",       
        'disable' => 0,        
        'price' => 59000,
        'option_id' => 25,
        'message' => [
            "Balo, Túi chéo, Túi rút phong cách Vpop giảm giá trên Lazada",                        
            "Giá khuyến mãi chỉ từ 59.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "áo khoác cotton nữ",       
        'disable' => 0,        
        'price' => 129000,        
        'message' => [
            "Xã hàng Áo khoác Cotton Nữ giảm giá sốc trên Lazada",                        
            "Giá khuyến mãi chỉ từ 129.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "áo khoác nam",       
        'disable' => 0,        
        'price' => 129000,        
        'message' => [
            "Xã hàng Áo khoác Nam giảm giá sốc trên Lazada",                        
            "Giá khuyến mãi chỉ từ 129.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],    
    [        
        'keyword' => "áo khoác nữ",       
        'disable' => 0,        
        'price' => 129000,        
        'message' => [
            "Xã hàng Áo khoác Nữ giảm giá sốc trên Lazada",                        
            "Giá khuyến mãi chỉ từ 129.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "áo khoác nữ xỏ ngón basix",       
        'disable' => 0,        
        'price' => 179000,        
        'message' => [
            "Xã hàng Áo Khoác Nữ Xỏ Ngón Basix Cao Cấp giảm giá sốc trên Lazada",                        
            "Giá khuyến mãi chỉ 179.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "áo khoác",       
        'disable' => 1,        
        'price' => 129000,        
        'message' => [
            "Xã hàng Áo khoác Nam Nữ giảm giá sốc trên Lazada",                        
            "Giá khuyến mãi chỉ từ 129.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],
    [        
        'keyword' => "túi rút",       
        'disable' => 0,        
        'price' => 59000,        
        'message' => [
            "Túi rút in hình cá tính giảm giá sốc trên Lazada",                        
            "Giá khuyến mãi chỉ từ 59.000 VND",
            "Xem chi tiết {link}",
            "✈ 🚏 🚕 🚄 Giao hàng miễn phí tòan quốc",            
        ]
    ],   
];

/************************************/
batch_info('BEGIN');        
$AppUI = unserialize(app_file_get_contents($authFilename));    
batch_info($AppUI->facebook_id . ' : ' . $AppUI->email);
$shareds = call('/facebookwallshares/all', [
        'user_id' => $AppUI->user_id,                           
        'site' => 'lazada.vn',                           
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
    if (empty($search['link'])) {
        $search['link'] = "https://vuongquocbalo.pushpad.xyz/p/2513?ui=false";
    }
    if (!empty($search['category_id'])) {
        $categories = (array) $search['category_id'];
    }
    $link = app_short_url($search['link']);
    $param = [
        'category_id' => implode(',', $categories),
        'limit' => $limit,
        'keyword' => !empty($search['keyword']) ? $search['keyword'] : '',      
        'no_create_image_facebook' => 1,
        'option_id' => !empty($search['option_id']) ? $search['option_id'] : '',        
    ];
    $productIds = [];    
    $products = call('/products/allforlazada', $param);
    if (empty($products)) {
        batch_info('Product not found');
        exit;
    }    
    $i = 0;    
    $data = [
        'message' => str_replace('{link}', $link, implode(PHP_EOL, $search['message'])),              
        'caption' => 'Lazada.vn'
    ];    
    foreach ($products as $product) {
        if (empty($product['url_image'])) {
            continue;
        }
        $product['category_id'] = explode(',', $product['category_id']);
        if (array_intersect([15, 16, 99], $product['category_id'])) {
            $product['link'] = app_short_url($product['url_lazada']);
            if (array_intersect([15], $product['category_id'])) {                
                $product['price'] = '159.000';
                $product['name'] = str_replace(['BL '], ['Balo Teen - Học sinh - Laptop '], $product['name']);
            } elseif (array_intersect([16], $product['category_id'])) {
                $product['price'] = '59.000';
            } elseif (array_intersect([99], $product['category_id'])) {
                $product['price'] = '79.000';
            }
            $photoData = [
                'caption' => implode(PHP_EOL, [ 
                    "{$product['name']}",                    
                    "Xem chi tiết {$product['link']}"
                ]),
                'url' => $product['url_image'],
            ];
            $photoId = uploadUnpublishedPhoto($photoData, $AppUI->fb_access_token, $errorMessage);
            if (!empty($photoId)) {   
                $data["attached_media[{$i}]"] = '{"media_fbid":"' . $photoId . '"}';
                $i++;
            }
        } else {              
            $product = call(
                '/products/detail', 
                array(                    
                    'product_id' => $product['product_id'],
                    'get_images' => 1
                )
            );
            $product['short'] = strip_tags($product['short']);
            $product['link'] = app_short_url($product['url_lazada']);
            if (!empty($search['price'])) { 
                $product['price'] = $search['price'];
            } else {
                $product['price'] = app_money_format($product['price']); 
            }
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
                                "{$product['name']}",                               
                                "{$product['short']}",                               
                                "Xem chi tiết {$product['link']}"
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
                'site' => 'lazada.vn',
            ]
        );
        $countPosted++;
    } else {
        batch_info("Post FAIL: {$errorMessage}");
    }    
}
batch_info('END');
exit;