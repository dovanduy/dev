<?php
$allowHour = [
//    0,
//    1,
//    2,
//    3,
//    4,
//    5,
//    6,
    7,
    8,
//    9,
    10,
    11,
//    12,
//    13,
    14,
    15,
//    16,
//    17,
    18,
    19,
    20,
    21,
//    22,
//    23
];
if (!isset($optionId)) {
    $optionId = 0;
}
function sleepToAllowHour() {
    global $allowHour;
    do {
        $h = (int)date('H');
        $m = (int)date('i');
        if (!in_array($h, $allowHour)) {  
            batch_info("Sleeping about " . (60 - $m) . ' minutes');
            sleep((60 - $m)*60);
        } else {
            break;
        }
    } while(true);
}

function writeResult($result) {
    global $file;
    if (!file_put_contents($file, implode(PHP_EOL, $result))) {
        batch_info("Can not write file");
        exit;
    }
}

sleepToAllowHour();

$dir = implode(DS, [getcwd(), 'album', $userId, $groupId]);

if (mk_dir($dir) === false) {
    batch_info('Error');
    exit;
}

$existed = 0;
$sharedProductId = [];
if (!empty($optionId)) {
    $file = implode(DS, [$dir, $categoryId . '_' . $optionId . '.txt']);
} else {
    $file = implode(DS, [$dir, $categoryId . '.txt']);
}
if (file_exists($file)) {
    batch_info("Album {$userId}:{$groupId}{$categoryId} have been already created");
    $existed = 1;    
    $content = file_get_contents($file);
    if ($content == false) { 
        batch_info('Can not read file ' . $file);
        exit;
    }         
    $album = explode(PHP_EOL, utf8_encode($content)); 
    $albumId = $album[0];
    foreach ($album as $i => $value) {
        if ($i > 0)  {
            $sharedProductId[] = end((explode(':', $value)));
        }
    }
   
    /*
    $album = file_get_contents($file);
    if ($album !== false) {       
        $album = explode(PHP_EOL, utf8_encode($album));
        $albumId = $album[0];
        do {
            $commentIcon = array_rand($commentList);    
            $commentMessage = $commentList[$commentIcon];
            $commentData = [
                'message' => $commentMessage,
                'attachment_url' => $commentIcon
            ];
        } while (in_array($commentIcon, $album));
        $commentId = commentToPost($albumId, $commentData, $accessToken, $errorMessage);
        if (!empty($commentId)) {
            $album[] = $commentIcon;           
            if (!file_put_contents($file, implode(PHP_EOL, $album))) {
                batch_info("Can not write file");
                exit;
            }
            batch_info("---Comment OK {$albumId}: {$commentId}");
        } else {
            batch_info("---Comment FAIL {$albumId}: {$errorMessage}");
        }
        exit;
    }
    * 
    */   
}

$param = [
    'website_id' => 1,
    'category_id' => $categoryId,
    'option_id' => isset($optionId) ? $optionId : 0,
    'not_in_product_id' => implode(',', $sharedProductId),
    'limit' => $existed ? 2 : 5
];

list($users, $products) = call('/users/fbadmin', $param);

if (empty($products)) {
    batch_info('Finish');
    exit;
}

foreach ($users as $user) {
    if ($userId == $user['facebook_id']) {
        $accessToken = $user['access_token'];
    }     
}
if (empty($accessToken)) {
    batch_info('Not found Access Token');
    exit;
}

if (empty($albumId)) {
    $albumId = groupCreateAlbum($groupId, $albumData, $accessToken);
}
if (!empty($albumId)) { 
    batch_info("Album Id: {$albumId}");
    $result = array(
        $albumId
    );
    writeResult($result);
    $count = 0;
    foreach ($products as $product) {
               
        // temp sleep to allowed time
        sleepToAllowHour();
        
        $price = app_money_format($product['price']);
        if (!empty($product['discount_percent'])) {
            $price .= ' (đang giảm giá: ' . $product['discount_percent'] . '%)';
        }
        $short = mb_ereg_replace('!\s+!', ' ', $product['short']); 
        $imageUrl = !empty($product['url_image']) ? $product['url_image'] : $product['image_facebook'];
        if (empty($imageUrl)) {
            continue;
        }   
        $photos = array();
        if (!empty($product['colors']) && count($product['colors']) > 1) {
            foreach ($product['colors'] as $color) {   
                $photos[] = array(
                    'name' => $product['name'] . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name'])),
                    'image_url' => $color['url_image'],
                );                
            }
        } else {
            $photos[] = array(
                'name' => $product['name'],
                'image_url' => $imageUrl,
            );
        }
        foreach ($photos as $photo) {            
            $data = [       
                'message' => implode(PHP_EOL, [
                    $photo['name'],
                    "Giá {$price}", 
                    "Mã hàng: {$product['code']}",                      
                    "NT đặt hàng: {$product['code']} gửi 098 65 60 997", 
                    "ĐT đặt hàng: 097 443 60 40 - 098 65 60 997",                 
                    $short,                 
                    "Chi tiết {$product['short_url']}",                 
                ]),
                'url' => $photo['image_url'],
                'no_story' => true
            ];
            $photoId = addPhotoToAlbum($albumId, $data, $accessToken); 
            if (empty($photoId)) {
                batch_info("Can not post a photo");
                writeResult($result);               
                exit;
            }            
            batch_info("Photo Id: {$photoId}");
            $result[] = $photoId . ':' . $product['product_id'];            
            if ($existed == 1 && $photo['image_url'] !== $photos[count($photos) - 1]['image_url']) {
                sleep(rand(4*60, 8*60));
            }
            $count++;
        }
    }
    writeResult($result);
}
echo PHP_EOL . 'Done';
exit;