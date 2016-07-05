<?php
// php cleanup_image.php
set_time_limit(24*60*60);
include ('base.php');
$param = [
    'website_id' => 1,    
    'has_image_facebook' => 1    
];
$docRoot = dirname(dirname(getcwd()));
$images = call('/images/allforbatch', $param);

$dbImages = array();
if (!empty($images)) {
    foreach ($images as $image) {
        if (!empty($image['url_image'])) {
            if (strpos($image['url_image'], 'http://img.vuongquocbalo.com') !== false) {
                $imagePath = str_replace('http://img.vuongquocbalo.com', implode(DS, array($docRoot, 'data', 'vuongquocbalo', 'img')), $image['url_image']);   
            } elseif (strpos($image['url_image'], 'http://img.vuongquocbalo.dev') !== false) {
                $imagePath = str_replace('http://img.vuongquocbalo.dev', implode(DS, array($docRoot, 'data', 'vuongquocbalo', 'img')), $image['url_image']);   
            }
            if (!empty($imagePath) && file_exists($imagePath)) {
                $dbImages[] = str_replace('/', DS, $imagePath);
            }
        } 
    }
}
$imageForFaceboks = call('/products/all', $param);
if (!empty($imageForFaceboks)) {
    foreach ($imageForFaceboks as $image) {
        if (strpos($image['image_facebook'], 'http://img.vuongquocbalo.com')) {
            $imagePath = str_replace('http://img.vuongquocbalo.com', implode(DS, array($docRoot, 'data', 'vuongquocbalo', 'img')), $image['image_facebook']);   
        } elseif (strpos($image['image_facebook'], 'http://img.vuongquocbalo.dev')) {
            $imagePath = str_replace('http://img.vuongquocbalo.dev', implode(DS, array($docRoot, 'data', 'vuongquocbalo', 'img')), $image['image_facebook']);   
        }
        if (!empty($imagePath) && file_exists($imagePath)) {
            $dbImages[] = str_replace('/', DS, $imagePath);
        }        
    }
}
$dirs = array(   
    implode(DS, array($docRoot, 'data', 'vuongquocbalo', 'img', '2016')),
);
$files = array();
foreach ($dirs as $path) {
    get_all_files($path, $files);    
}
batch_info('BEGIN');
batch_info('Total db image: ' . count($images));
batch_info('Total storage image: ' . count($files));

$result = array();
foreach ($files as $file) {
    if (!in_array($file, $dbImages) 
        && is_file($file) 
        && file_exists($file)) {
        $fileName = basename($file);
        $backupDestination = str_replace(array(DS . 'img' . DS, $fileName), array(DS . 'backup' . DS, ''), $file);        
        if (mk_dir($backupDestination) === false) {
            batch_info('Error');
            exit;
        }
        if (copy($file, $backupDestination . $fileName)) {
            unlink($file);
        }
        batch_info(str_replace(implode(DS, [$docRoot, 'data', 'vuongquocbalo']), '', $file) . ' -> ' . str_replace(implode(DS, [$docRoot, 'data', 'vuongquocbalo']), '', $backupDestination . $fileName));
        $result[] = $file;
    }
}
batch_info('Total deleted image: ' . count($result));
batch_info('END');
exit;