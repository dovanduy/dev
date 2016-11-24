<?php
// php update_content.php
set_time_limit(24*60*60);
include ('base.php');
$websiteId = 1;
$categories = [2, 3, 5, 6, 15, 16, 20, 21];
foreach ($categories as $categoryId) {
	$fileProducts = $categoryId . '_products.serialize';
    if (file_exists($fileProducts)) {
        $products = unserialize(app_file_get_contents($fileProducts));  
    } else {
        batch_info('Error read file');
        exit;
    }
	batch_info('BEGIN ' . $categoryId);
	if (!empty($products)) {
		foreach ($products as $product) { 
			$code = $product['code'];
			$param = [
                'website_id' => $websiteId,
				'code' => $code,
				'add_image_to_content' => 1,				
			];	
            if (!in_array($categoryId, [15, 16])) {
                $param['content'] = $product['content'];
            }
			$ok = call('/products/updatelocale', $param);
			if ($ok) {
				batch_info($code . ' updated');  
			} else {
				batch_info($code . ' fail');
			}
		}
	}
	batch_info('END');
}
exit;