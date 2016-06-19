<?php
include ('base.php');

$userId = '125965971158216';
$groupId = '113462365452492';
$categoryId = 18;

$dir = implode(DS, [getcwd(), 'album', $userId, $groupId]);

if (mk_dir($dir) === false) {
    echo PHP_EOL . 'Error';
    exit;
}

$param = [
    'website_id' => 1,
    'category_id' => $categoryId,
    'limit' => 1000
];

list($users, $products) = call('/users/fbadmin', $param);
if (empty($users) || empty($products)) {
    echo 'Empty';
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

$file = implode(DS, [$dir, $categoryId . '.txt']);
if (file_exists($file)) {
    batch_info("Album {$userId}:{$groupId}{$categoryId} have been already created");
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
    exit;
}

$data = [
    'name' => 'Balo nam đẹp, sành điệu, giá tốt nhất thị trường',
    'message' => implode(PHP_EOL, [
        '✓ NT đặt hàng: MH1,MH2,MH3 gửi 098 65 60 997 (MH: mã hàng)',
        '✓ ĐT đặt hàng: 097 443 60 40 - 098 65 60 997',        
        '✓ Xem chi tiết và giá bán trong hình nhé các Hot boy',
        '✓ Giao hàng TOÀN QUỐC. Free ship ở khu vực nội thành TP HCM (các quận 1, 2, 3, 4 ,5 ,6 ,7 ,8 ,10, 11, Bình Thạnh, Gò Vấp, Phú Nhuận, Tân Bình, Tân Phú)',
        '✓ Khám phá hàng nghìn balo, túi xách đẹp, chất lượng, giá tốt trên website vuongquocbalo.com',
    ]),    
];

$albumId = groupCreateAlbum($groupId, $data, $accessToken);
if (!empty($albumId)) { 
    batch_info("Album Id: {$albumId}");
    $result = array(
        $albumId
    );
    foreach ($products as $i => $product) {
        $price = app_money_format($product['price']);
        if (!empty($product['discount_percent'])) {
            $price .= ' (giảm giá: ' . $product['discount_percent'] . '%)';
        }   
        $short = mb_ereg_replace('!\s+!', ' ', $product['short']); 
        $imageUrl = !empty($product['url_image']) ? $product['url_image'] : $product['image_facebook'];
        if (empty($imageUrl)) {
            continue;
        }
        if ($i > 0) {
            sleep(rand(5*60, 10*60));
        }       
        $data = [       
            'message' => implode(PHP_EOL, [
                $product['name'], 
                "Mã hàng: {$product['code']}", 
                "Giá {$price}",   
                "NT đặt hàng: {$product['code']} gửi 098 65 60 997", 
                "ĐT đặt hàng: 097 443 60 40 - 098 65 60 997",                 
                $short,                 
                "Chi tiết {$product['short_url']}",                 
            ]),
            'url' => $imageUrl
        ];
        $photoId = addPhotoToAlbum($albumId, $data, $accessToken); 
        if (!empty($photoId)) {
            batch_info("Photo Id: {$photoId}");
            $result[] = $photoId;
        } else {
            break;
        }        
    }
    if (!file_put_contents($file, implode(PHP_EOL, $result))) {
        batch_info("Can not write file");
        exit;
    }
}
echo PHP_EOL . 'Done';
exit;