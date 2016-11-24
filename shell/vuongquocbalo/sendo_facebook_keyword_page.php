<?php
/*
   php /home/vuong761/public_html/shell/vuongquocbalo/sendo_facebook_keyword_page.php
 * Balo in
 */
include ('base.php');
$pageId = '306098189741513';
$searchs = [
    [
        'name' => "Balo và Túi rút in hình Pikachu dễ thương",
        'keyword' => "pikachu",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=pikachu",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình Doremon dễ thương",
        'keyword' => "doraemon",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=doremon",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình Naruto",
        'keyword' => "naruto",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=naruto",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình Kakashi",
        'keyword' => "kakashi",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=kakashi",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình Pokemon dễ thương",
        'keyword' => "pokemon",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=pokemon",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình Gấu Panda dễ thương",
        'keyword' => "panda",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=panda",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình ca sĩ Khởi My dễ thương",
        'keyword' => "khoi my",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=khoi my",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình ca sĩ Noo Phước Thịnh",
        'keyword' => "noo",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=noo",
        'disable' => 0,
    ],
    [
        'name' => "Balo và Túi rút in hình Nhóm Nhạc 365",
        'keyword' => "365",
        'link' => "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=365",
        'disable' => 0,
    ],
];
$searchs = app_array_rand($searchs, 1);
$track = 'utm_source=facebook&utm_medium=social&utm_campaign=search_sendo';
$categories = [15, 16];
$limit = 300;
$authFilename = 'facebook_login5.serialize';
if (file_exists($authFilename)) {   
    batch_info('BEGIN');        
    $AppUI = unserialize(app_file_get_contents($authFilename));     
    $shareds = call('/facebookwallshares/all', [
            'user_id' => $AppUI->user_id,  
            'page_id' => $pageId,
        ]
    );   
    foreach ($searchs as $search) {  
        if (!empty($search['disable'])) {
            continue;
        }
        foreach ($shareds as $shared) {  
            if ($shared['keyword'] == $search['keyword']) {
                continue;
            }
        }
        if (empty($search['link'])) {
            $search['link'] = "https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q={$search['keyword']}";
        }
        $link = app_short_url($search['link']);
        $products = call('/products/allforsendo', [
                'category_id' => implode(',', $categories), 
                'limit' => $limit,                
                'keyword' => $search['keyword'],
                'no_create_image_facebook' => 1,
            ]
        );
        if (empty($products)) {
            batch_info('Product not found');
            exit;
        }
        $i = 0;  
        $data = [
            'message' => implode(PHP_EOL, [
                "💼 {$search['name']}",
                "💰 Balo lớn: 187.000, Balo nhỏ 159.000, Túi rút: 69.000 VNĐ",
                "📞 098 65 60 943",            
                "❝ Hàng Việt Nam xuất khẩu, chất liệu simili 100% không thấm nước, không bong tróc. dễ lau chùi khi bị bẩn - chi tiết {$link} ❞",            
                "✈ 🚏 🚕 🚄 Ship TOÀN QUỐC",            
            ]),              
            'caption' => 'Shop Balo Học Sinh, Balo Teen'
        ];
        foreach ($products as $product) {
            if (empty($product['url_image'])) {
                continue;
            }
            if (array_intersect([15], (array) $product['category_id'])) { 
                if (!empty($product['url_sendo1'])) {  
                    $code = 'S' . $product['code_src'];
                    $product['price'] = '159.000';
                    $product['name'] = str_replace([$product['code'], 'BL ',], [$code, 'Balo Ipad - Học thêm - Đi chơi '], $product['name']);
                    $product['code'] = $code;
                } else {
                    $product['price'] = '187.000';
                    $product['name'] = str_replace(['BL '], ['Balo Teen - Học sinh - Laptop '], $product['name']);
                }               
            } elseif (array_intersect([16], (array) $product['category_id'])) {
                $product['price'] = '69.000';
            }		          
            $photoData = [
                'caption' => implode(PHP_EOL, [ 
                    "💼 {$product['name']}",
                    "💰 {$product['price']} VNĐ",   
                    "Mã hàng: {$product['code']}",           
                    "Nhắn tin đặt hàng: {$product['code']} gửi 098 65 60 943",           
                    "Xem chi tiết {$link}"
                ]),
                'url' => $product['url_image'],
            ];
            $photoId = uploadUnpublishedPhoto($photoData, $AppUI->fb_access_token, $errorMessage);
            if (!empty($photoId)) {   
                $data["attached_media[{$i}]"] = '{"media_fbid":"' . $photoId . '"}';
                $i++;
            }
        }
        $postId = postToPage($pageId, $data, $AppUI->fb_access_token, $errorMessage);
        if (!empty($postId)) {
            batch_info("Post OK: {$postId}");
            call('/facebookwallshares/add', 
                [
                    'user_id' => $AppUI->user_id,           
                    'facebook_id' => $AppUI->facebook_id,   
                    'page_id' => $pageId,
                    'keyword' => $search['keyword'],
                    'social_id' => $postId,
                ]
            );
        } else {
            batch_info("---Post FAIL: {$errorMessage}");
        }
        //sleep(rand(5*60, 10*60));
    }
    batch_info('END');
} else {
    batch_info('Token does not exists');
}
exit;