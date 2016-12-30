<?php

// php pa.php 101
include_once 'base.php';
$websiteId = 1;
$categoryId = 101;
$fileProducts = 'ao_khoac.php';
if (file_exists($fileProducts)) {
    $products = include_once 'ao_khoac.php';
	batch_info('BEGIN: Import Product');
	batch_info('Total product: ' . count($products));
	$count = 1;
	$priority = count($products);
	foreach ($products as $product) {
        $product['import_attributes'] = array_values($product['import_attributes']);
        //p($product, 1);
		$product['website_id'] = $websiteId;
		$product['short'] = str_replace(PHP_EOL, '<br>', $product['short']);
		$product['content'] = str_replace(PHP_EOL, '<br>', $product['content']);
		$product['priority'] = $priority;
		//$product['add_image_to_content'] = 0;                    
		$_id = call('/products/add', $product, $errors);
		if ($_id) {
			batch_info('[' . $count . '] ' . $product['code'] . ' Done');
		} else {
			batch_info('[' . $count . '] ' . $product['code'] . ' Failed');
			batch_info($product['url_src']);
			$fails[] = $product;
		}
		$priority--;
		$count++;
	}
	batch_info('END: Import Product');
	batch_info('Done');
}
exit;
