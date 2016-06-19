<?php
set_time_limit(24*60*60);
include ('base.php');
$param = [
    'website_id' => 1,    
    'has_image_facebook' => 1    
];
$docRoot = dirname(getcwd());
$images = call('/images/allforbatch', $param);
$dbImages = array();
if (!empty($images)) {
    foreach ($images as $image) {
        if (!empty($image['url_image'])) {
            $imagePath = str_replace('http://img.vuongquocbalo.com', implode(DS, array($docRoot, 'data', 'upload', 'img')), $image['url_image']);   
            $dbImages[] = str_replace('/', DS, $imagePath);
        }    
    }
}
$imageForFaceboks = call('/products/all', $param);
if (!empty($imageForFaceboks)) {
    foreach ($imageForFaceboks as $image) {
        if (!empty($image['image_facebook'])) {
            $imagePath = str_replace('http://img.vuongquocbalo.com', implode(DS, array($docRoot, 'data', 'upload', 'img')), $image['image_facebook']);   
            $dbImages[] = str_replace('/', DS, $imagePath);
        }
    }
}
$dirs = array(   
    implode(DS, array($docRoot, 'data', 'upload', 'img', '2016')),
);
$files = array();
foreach ($dirs as $path) {
    get_all_files($path, $files);    
}
echo PHP_EOL . 'Begin';
echo PHP_EOL . 'Total image: ' . count($images);
$result = array();
foreach ($files as $file) {
    if (!in_array($file, $dbImages) 
        && is_file($file) 
        && file_exists($file)) {
        $fileName = basename($file);
        $backupDestination = str_replace(array(DS . 'img' . DS, $fileName), array(DS . 'backup' . DS, ''), $file);        
        if (mk_dir($backupDestination) === false) {
            echo PHP_EOL . 'Error';
            exit;
        }
        if (copy($file, $backupDestination . $fileName)) {
            unlink($file);
        }
        echo PHP_EOL . str_replace(implode(DS, [$docRoot, 'data', 'upload']), '', $file) . ' -> ' . str_replace(implode(DS, [$docRoot, 'data', 'upload']), '', $backupDestination . $fileName);
        $result[] = $file;
    }
}
echo PHP_EOL . 'Total deleted image: ' . count($result);
echo PHP_EOL . 'End';
exit;