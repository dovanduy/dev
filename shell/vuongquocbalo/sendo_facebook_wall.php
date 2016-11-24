<?php

include ('base.php');
if (file_exists($authFilename)) {   
    batch_info('BEGIN');
    $limit = 2;
    $AppUI = unserialize(app_file_get_contents($authFilename));  
	$categoryId = $categories[array_rand($categories, 1)];
	$products = call('/products/allforsendo', [
            'category_id' => $categoryId, 
            'limit' => $limit,
            'for_wall' => 1
        ]
    );
    if (empty($products)) {
        batch_info('Product not found');
        exit;
    }
	foreach ($products as $product) {     
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
		$product['short_url'] = app_short_url($product['url']); 
		$data = app_get_fb_share_content($product, $caption);
		$postId = postToWall($data, $AppUI->fb_access_token, $errorMessage);
		if (!empty($postId)) {   
            call('/productfacebookpageshares/add', [
                'user_id' => $AppUI->user_id,                
                'facebook_id' => $AppUI->facebook_id,                
                'page_id' => '',
                'social_id' => $postId,                
                'product_id' => $product['product_id'],
            ]);
			batch_info("Post OK: {$postId}");
		} else {
			batch_info("---Post FAIL: {$errorMessage}");
		}
		sleep(rand(1*60, 3*60));
	}
    batch_info('END');
} else {
    batch_info('Token does not exists');
}
exit;