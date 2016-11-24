<?php

/*
  php /home/vuong761/public_html/shell/thoitrang1/sendo_facebook.php 
 * 
 */

include ('base.php');
if (file_exists('facebook_login.serialize')) {
    sleep(rand(1*60, 15*60));
    $limit = 1;
    $AppUI = unserialize(app_file_get_contents('facebook_login.serialize'));
    $categories = [61, 66];
    $categories = [51, 76];
    $groups = app_array_rand(app_facebook_groups(), 4);
    foreach ($groups as $groupId) {        
        $categoryId = $categories[array_rand($categories, 1)];       
        $products = call('/products/allforsendo', ['category_id' => $categoryId, 'limit' => $limit]);
        $result = array();
        foreach ($products as $product) {         
            $product = call('/products/detail', ['product_id' => $product['product_id']]);
            $caption = 'Shop Balo Học Sinh, Balo Teen';
            if (array_intersect([15], $product['category_id'])) { 
                $product['price'] = '178000';                
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
            $product['short_url'] = app_short_url($product['url']); 
            $data = app_get_fb_share_content($product, $caption);
            $postId = postToGroup($groupId, $data, $AppUI->fb_access_token, $errorMessage);
            if (!empty($postId)) {                
                batch_info("Post OK {$groupId}: {$postId}");
            } else {
                batch_info("---Post FAIL {$groupId}: {$errorMessage}");
            }            
        }
        sleep(rand(5*60, 10*60));     
    }
    batch_info('Done');
} else {
    batch_info('Token does not exists');
}
exit;