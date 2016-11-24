<?php
// php /home/vuong761/public_html/shell/vuongquocbalo/changepath_image.php
// php changepath_image.php 1
// php changepath_image.php 2
// php changepath_image.php 4
// php changepath_image.php 5
// php changepath_image.php 14
// php changepath_image.php 98
// php changepath_image.php 101
// php changepath_image.php 102

set_time_limit(24*60*60);
include ('base.php');

$categoryId = isset($argv[1]) ? $argv[1] : 0;
$param = [
    'website_id' => $websiteId,
    'product_images_only' => 1,   
    'category_id' => $categoryId, 
    //'product_id' => '486,2714,2716,3098,6459', 
];
if (!empty($param['product_id'])) {
    $categoryId = '2,3,5,6,15,16,20,21,19';
    $param['category_id'] = $categoryId;
}
$images = call('/images/allforbatch', $param);
$productIds = [];
batch_info('BEGIN');
foreach ($images as $image) {      
    $productCode = name_2_url($image['product_code']);
    $categoryName = name_2_url($image['category_name']);
    $partten = str_replace('/', '\/', $imgDomain)  . '\/([0-9]{4})\/([0-9]{2})\/([0-9]{2})\/(.*)';
    //p($partten, 1);
    batch_info($image['url_image']);
    preg_match("/{$partten}/i", $image['url_image'], $matches);
    if (!isset($matches[4])) {       
        continue;
    }    
    if (!in_array($image['product_id'], $productIds)) {
        $productIds[] = $image['product_id'];
    }
    $fileName = $matches[4];
    if (strpos($image['url_image'], $imgDomain) !== false) {
        $imagePath = str_replace($imgDomain, $imgDir, $image['url_image']);
    }
    if (!empty($imagePath) && !file_exists($imagePath)) { 
        $imageUrl = call('/images/upload', [            
            'url_image' => $image['url_image_source']
        ]);
        if (!empty($imageUrl)) {
            batch_info($imageUrl);
            $imagePath = str_replace($imgDomain, $imgDir, $imageUrl);
        }
    }
    if (!empty($imagePath) && file_exists($imagePath)) {        
        $newDestination = implode(DS, [$imgDir, $categoryName, $productCode]);
        batch_info($newDestination);
        if (mk_dir($newDestination) === false) {
            batch_info('Error 2');
            exit;
        }
        if (copy($imagePath, $newDestination . DS . $fileName)) {   
            unlink($imagePath);
            $newImageUrl = str_replace([$imgDir, DS], [$imgDomain, '/'], $newDestination . DS . $fileName);
            call('/images/update', [
                'id' => $image['image_id'], 
                'src' => 'products', 
                'url_image' => $newImageUrl
            ]);
            batch_info($newImageUrl);            
        }
    }
}
if (!empty($productIds)) {
    foreach ($productIds as $productId) {    
        $ok = call('/products/updatelocale', [
            'product_id' => $productId,
            'add_image_to_content' => 1,
            'website_id' => $websiteId,
        ]);
        if ($ok) {
            batch_info($productId . ' updated');  
        } else {
            batch_info($productId . ' failed'); 
        }
    }
}
batch_info('END');
exit;