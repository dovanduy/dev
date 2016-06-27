<?php
// php /home/vuong761/public_html/batch/album_hoimuabanraovattoanquoc.php
include ('base.php');
$docRoot = dirname(getcwd());
$groupId = '795251457184853';
$dir = implode(DS, [$docRoot, 'fbalbum', $groupId]);
if (mk_dir($dir) === false) {
    batch_info('Error');
    exit;
}
do {
    $waitingFile = implode(DS, [$docRoot, 'data', 'fbalbum', 'waiting.txt']); 
    $h = (int)date('H');
    if ($h >= 23) {
        if (file_exists($waitingFile)) {
            unlink($waitingFile);
        }
        batch_info('Done');
        exit;
    }      
    if (file_exists($waitingFile)) {       
        $content = file_get_contents($waitingFile);
        if (empty($content)) {
            batch_info('Can not read file');
            exit;
        }
        $data = json_decode($content, true);
        foreach ($data as $row) {
            $userId = $row['userId'];
            $accessToken = $row['accessToken'];  
            $product = $row['product'];
            $file = implode(DS, [$dir, $product['product_id'] . '.txt']);
            if (file_exists($file)) {
                continue;
            }
            $short = mb_ereg_replace('!\s+!', ' ', $product['short']);
            $price = app_money_format($product['price']);            
            if (!empty($product['discount_percent'])) {
                $price .= ' (-' . $product['discount_percent'] . '%)';
            }
            $albumData = [
                'name' => $product['name'],
                'message' => implode(PHP_EOL, [
                    "Giá: {$price}",
                    "Mã hàng: {$product['code']}",
                    "Nhắn tin đặt hàng: {$product['code']} gửi 098 65 60 997",
                    "Điện thoại đặt hàng: 097 443 60 40 - 098 65 60 997",   
                    "{$short}",
                    "Chi tiết {$product['short_url']}",
                    "Giao hàng TOÀN QUỐC. Free ship ở khu vực nội thành TP HCM (các quận 1, 2, 3, 4 ,5 ,6 ,7 ,8 ,10, 11, Bình Thạnh, Gò Vấp, Phú Nhuận, Tân Bình, Tân Phú)",
                ]),
            ];
            $albumId = groupCreateAlbum($groupId, $albumData, $accessToken, $errorMessage);
            if (empty($albumId)) {
                batch_info($errorMessage);      
                exit;
            }   
            batch_info("Album {$albumId}");
            $result[] = $albumId;
            $photos = array();
            if (!empty($product['colors']) && count($product['colors']) > 1) {
                foreach ($product['colors'] as $color) {   
                    $photos[$color['url_image']] = $product['name'] . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name']));
                }
            } else {
                $photos[$product['url_image']] = $product['url_image'];
            }               
            if (!empty($product['images'])) {
                foreach ($product['images'] as $image) {   
                    if (!isset($photos[$image['url_image']])) {
                        $photos[$image['url_image']] = $product['name'];
                    }
                }
            }
            foreach ($photos as $imageUrl => $name) {            
                $data = [       
                    'message' => implode(PHP_EOL, [
                        $name,
                        "Giá: {$price}",
                        $short,         
                        "Chi tiết {$product['short_url']}",
                    ]),
                    'url' => $imageUrl,
                    'no_story' => true
                ];
                $photoId = addPhotoToAlbum($albumId, $data, $accessToken, $errorMessage); 
                if (!empty($photoId)) {
                    batch_info("Photo {$photoId}");
                    $result[] = $photoId;                    
                } else {
                    batch_info($albumId . ':' . $errorMessage);
                    exit;           						
                }
            }            
            if (!empty($result)) {                
                file_put_contents($file, implode(PHP_EOL, $result));                
            }
            sleep(rand(30*60, 45*60));
        }
    }
    sleep(rand(30*60, 60*60));  
} while (1);
