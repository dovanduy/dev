<?php
// php cleanup_image.php
set_time_limit(24*60*60);
include ('base.php');
$param = [
    'website_id' => 2,    
    'has_image_facebook' => 1    
];
$docRoot = dirname(dirname(getcwd()));
$images = call('/images/allforbatch', $param);
$dbImages = array();
if (!empty($images)) {
    foreach ($images as $image) {
        if (!empty($image['url_image'])) {
            if (strpos($image['url_image'], 'http://img.thoitrang1.net') !== false) {
                $imagePath = str_replace('http://img.thoitrang1.net', implode(DS, array($docRoot, 'data', 'thoitrang1', 'img')), $image['url_image']);   
            } elseif (strpos($image['url_image'], 'http://img.thoitrang1.vn') !== false) {
                $imagePath = str_replace('http://img.thoitrang1.vn', implode(DS, array($docRoot, 'data', 'thoitrang1', 'img')), $image['url_image']);   
            }
            if (!empty($imagePath) && file_exists($imagePath)) {
                $dbImages[] = str_replace('/', DS, $imagePath);
            }
        } 
    }
}
if (empty($dbImages)) {
    batch_info('Error get db images');
    exit;
}
$imageForFaceboks = call('/products/all', $param);
if (!empty($imageForFaceboks)) {
    foreach ($imageForFaceboks as $image) {
        if (strpos($image['image_facebook'], 'http://img.thoitrang1.net')) {
            $imagePath = str_replace('http://img.thoitrang1.net', implode(DS, array($docRoot, 'data', 'thoitrang1', 'img')), $image['image_facebook']);   
        } elseif (strpos($image['image_facebook'], 'http://img.thoitrang1.vn')) {
            $imagePath = str_replace('http://img.thoitrang1.vn', implode(DS, array($docRoot, 'data', 'thoitrang1', 'img')), $image['image_facebook']);   
        }
        if (!empty($imagePath) && file_exists($imagePath)) {
            $dbImages[] = str_replace('/', DS, $imagePath);
        }        
    }
}
$dirs = array(   
    implode(DS, array($docRoot, 'data', 'thoitrang1', 'img', '2016')),
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
        batch_info(str_replace(implode(DS, [$docRoot, 'data', 'thoitrang1']), '', $file) . ' -> ' . str_replace(implode(DS, [$docRoot, 'data', 'thoitrang1']), '', $backupDestination . $fileName));
        $result[] = $file;
    }
}
batch_info('Total deleted image: ' . count($result));
batch_info('END');
exit;