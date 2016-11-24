<?php
/*
   php /home/vuong761/public_html/shell/vuongquocbalo/facebook_page_comment.php
 * Balo in
 */
include ('base.php');
$auth = ['facebook_login4.serialize', 'facebook_login5.serialize'];
$authFilename = $auth[array_rand($auth, 1)];
$authFilename = 'facebook_login5.serialize';
$userId = 49;
if (!file_exists($authFilename)) {
    batch_info('Token does not exists');
    exit;
}
// https://www.facebook.com/truyencuoihay/posts/1077767485653720
// https://www.facebook.com/truyencuoihay/posts/1078645948899207
$AppUI = unserialize(app_file_get_contents($authFilename));
$postId = '147671678663310_1078645948899207';
$postId = '147671678663310_1077767485653720';
$data = app_get_fb_share_comment();
$commentId = commentToPost($postId, $data, $AppUI->fb_access_token, $errorMessage, $errorCode);
if (!empty($commentId)) {
    batch_info("Comment OK {$postId}: {$commentId}");                    
} else {        
    batch_info("Comment FAIL {$postId}: {$errorMessage}");                    
}  
batch_info('Done');
exit;
    
$categoryId = '15,16';
$products = call('/products/allforsendo', [
        'category_id' => $categoryId, 
        'limit' => 1,
        'no_create_image_facebook' => 1
    ]
);
if (empty($products)) {
    batch_info('Product not found');
    exit;
}
foreach ($products as $product) {     
    $image = $product['url_image'];
    $product = call('/products/detail', ['product_id' => $product['product_id']]);
    $caption = 'Shop Balo Học Sinh, Balo Teen';
    if (array_intersect([15], $product['category_id'])) { 
        if (!empty($product['url_sendo1'])) {  
            $code = 'S' . $product['code_src'];
            $product['price'] = '159000';
            $product['name'] = str_replace([$product['code'], 'BL ',], [$code, 'Balo Ipad - Học thêm - Đi chơi '], $product['name']);
            $product['code'] = $code;
        } else {
            $product['price'] = '187000';
            $product['name'] = str_replace(['BL '], ['Balo Học sinh - Teen '], $product['name']);
        }               
    } elseif (array_intersect([16], $product['category_id'])) {
        $product['price'] = '69000';
    } else { 
        $product['price'] = $product['price'] - round((5/100)*$product['price'], -3);
        $caption = 'Zanado Shop';
    }
    $data = app_get_fb_share_comment();
    /*
    $data['message'] .= PHP_EOL . implode(PHP_EOL, [
        "",
        "💼 {$product['name']}",
        "💰 {$product['price']}",             
        "📞 098 65 60 943",
        "chi tiết - " . app_short_url($product['url_other'])
    ]);
    $data['attachment_url'] = $image;    
    * 
    */
    $commentId = commentToPost($postId, $data, $AppUI->fb_access_token, $errorMessage, $errorCode);
    if (!empty($commentId)) {
        batch_info("Comment OK {$postId}: {$commentId}");                    
    } else {        
        batch_info("Comment FAIL {$postId}: {$errorMessage}");                    
    }  
    p($data);
}
batch_info('Done');
exit;