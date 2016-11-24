<?php

/*
  php sendo_facebook_wall3.php	
  php /home/vuong761/public_html/shell/vuongquocbalo/sendo_facebook_wall3.php
  facebook_group_id
  '794951187227341', 	// Adm: fb.ngocai@gmail.com, https://www.facebook.com/groups/chosaletonghopbmt/
  '487699031377171', 	// Adm: fb.huean@outlook.com, https://www.facebook.com/groups/muabansaigoncholon/
  '952553334783243', 	// Chợ online Khang Điền Q.9 https://www.facebook.com/groups/928701673904347/
  '928701673904347', 	// Adm: fb.huean@outlook.com, Chợ sinh viên giá rẻ https://www.facebook.com/groups/928701673904347/
  '1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
  '297906577042130', 	// Hội những người mê kinh doanh online
  '519581824789114', 	// Adm: kenstore2016@gmail.com, https://www.facebook.com/groups/choraovatvaquangcao/, CHỢ RAO VẶT & QUẢNG CÁO ONLINE
  '209799659176359', 	// Rao vặt linh tinh
  '190283764491669', 	// Adm: fb.ngocai@gmail.com, https://www.facebook.com/groups/Hoicacbamemuahangthongminh/
  '113462365452492', 	// Adm: mail.vuongquocbalo.com@gmail.com, https://www.facebook.com/groups/24hmuabanraovat/
  '795251457184853', 	// Adm: fb.huean@outlook.com, https://www.facebook.com/groups/795251457184853/ HỘI MUA BÁN-RAO VẶT-GIAO LƯU KẾT BẠN TOÀN QUỐC
 * 
 */

include ('base.php');
if (file_exists('facebook_login3.serialize')) {   
    batch_info('BEGIN');
    $limit = 1;
    $AppUI = unserialize(app_file_get_contents('facebook_login3.serialize'));
    $categories = [15, 16];
	$categoryId = $categories[array_rand($categories, 1)];
	$products = call('/products/allforsendo', [
            'category_id' => $categoryId, 
            'limit' => $limit,
            'for_wall' => 1
        ]
    );	
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
        //$product['tags'] = app_facebook_tags($AppUI->facebook_id);
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