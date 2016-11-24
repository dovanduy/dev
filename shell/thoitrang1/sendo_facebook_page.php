<?php

/*  	
   php /home/vuong761/public_html/shell/thoitrang1/sendo_facebook_page.php
 * php sendo_facebook_page.php
 * 
 */

include ('base.php');
$pageId = '299119777110220';
if (file_exists('facebook_login2.serialize')) {   
    batch_info('BEGIN');
    $limit = 2;
    $AppUI = unserialize(app_file_get_contents('facebook_login2.serialize'));   
	$categories = [1, 4, 50, 59, 64, 70, 75, 83];
	$categoryId = $categories[array_rand($categories, 1)];
	$products = call('/products/allforsendo', ['page_id' => $pageId, 'category_id' => $categoryId, 'limit' => $limit]);	
	$result = array();
	foreach ($products as $product) {     
		$product = call('/products/detail', ['product_id' => $product['product_id']]);
		$caption = 'Shop Balo Học Sinh, Balo Teen';
        if (array_intersect([15], $product['category_id'])) { 
            $product['price'] = '187000';                
            if ($product['code'] == 'S' . $product['code_src']) {
                $product['price'] = '159000';                
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
		$product['short_url'] = app_short_url($product['url']); 
		$data = app_get_fb_share_content($product, $caption);
		$postId = postToPage($pageId, $data, $AppUI->fb_access_token, $errorMessage);
		if (!empty($postId)) {   
            call('/productfacebookpageshares/add', [
                'user_id' => $AppUI->user_id,                
                'facebook_id' => $AppUI->facebook_id,                
                'page_id' => $pageId,
                'social_id' => $postId,                
                'product_id' => $product['product_id']
            ]);
			batch_info("Post OK {$pageId}: {$postId}");
		} else {
			batch_info("---Post FAIL {$pageId}: {$errorMessage}");
		}
		sleep(rand(1*60, 3*60));
	}
    batch_info('END');
} else {
    batch_info('Token does not exists');
}
exit;