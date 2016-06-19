<?php
/*
    php <file_name> user_id category_id group_id
    php /home/vuong761/public_html/batch/group.php 126728971080640 8 519581824789114
    php /home/vuong761/public_html/batch/group.php 107846242974801 4 928701673904347
    php /home/vuong761/public_html/batch/group.php 127283041026900 16 794951187227341
    
     facebook_user_id 
    '10206637393356602', // Thai Lai
    '129881887426347', // vuongquocbalo.com
    '835521976592060', // Ngoc Nguyen My
    '1723524741251993', // Duc Tin
    '126728971080640', // kenstore2016@gmail.com https://www.facebook.com/kinhdothoitrang.vn
    '107846242974801', // fb.huean@outlook.com
    '127283041026900', // fb.ngocai@gmail.com
    '116860312071059', // fb.hoaian@gmail.com
    '103326903428052', // fb.minhan@outlook.com
    
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
    
    category_id 
    1	Ba lô, cập học sinh
    2	Ba lô, túi xách Nữ đẹp
    3	Ba lô in giả da
    4	Ba lô in giây rút
    5	Ba lô mẫu giáo
    6	Ba lô laptop
    7	Ba  lô in 2 màu
    8	Túi xách nữ
    9	Túi chéo sinh viên
    10	Ba lô, túi xách du lịch
    11	Ba lô độc, đẹp
    12	Ba lô, túi xách Nam đẹp
    13	Ba lô học sinh cấp 1
    14	Ba lô học sinh cấp 2,3
    15	Ba lô teen
    16	Ba lô, túi chéo sinh viên
    17	Ba lô Nữ
    18	Ba lô Nam
    19	Túi xách, Cặp táp Nam
    20	Ba lô sinh viên
 * 
 */

include ('base.php');

$dir = implode(DS, [getcwd(), 'group', $groupId, $userId]);

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

if (mk_dir($dir) === false) {
    echo PHP_EOL . 'Error';
    exit;
}

$tags = array();
foreach ($tagIds as $friendId) {
    if ($userId != $friendId) {
        $tags[] = $friendId;
    }
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

function __comment($product, $postId) {
    global $accessToken;    
    $product['price'] = app_money_format($product['price']);
    if (!empty($product['colors']) && count($product['colors']) > 1) {
        foreach ($product['colors'] as $color) {   
            sleep(rand(4*60, 8*60));
            $commentData = [
                'message' => $product['name'] 
                    . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name']))
                    . ' - Giá ' . $product['price']                     
                    . ' - ĐT đặt hàng 097 443 60 40 - 098 65 60 997'
                    . ' - Chi tiết ' . $product['short_url'],
                'attachment_url' => $color['url_image'],
            ];
            $commentId = commentToPost($postId, $commentData, $accessToken, $errorMessage);
            if (!empty($commentId)) {
                batch_info("---Comment OK {$postId}: {$commentId}");
            } else {
                batch_info("---Comment FAIL {$postId}: {$errorMessage}");
            }
        }
    } elseif (!empty($product['image_facebook'])) {        
        sleep(rand(4*60, 8*60));
        $commentData = [
            'message' => $product['name']                     
                    . ' - Giá ' . $product['price']                     
                    . ' - ĐT đặt hàng 097 443 60 40 - 098 65 60 997'
                    . ' - Chi tiết ' . $product['short_url'],
            'attachment_url' => $product['image_facebook'],
        ];
        $commentId = commentToPost($postId, $commentData, $accessToken, $errorMessage);
        if (!empty($commentId)) {
            batch_info("---Comment OK {$postId}: {$commentId}");
        } else {
            batch_info("---Comment FAIL {$postId}: {$errorMessage}");
        }
    }
}

$result = array();
foreach ($products as $product) {            
    if (empty($postId)) {
        $file = implode(DS, [$dir, $product['product_id'] . '.txt']);
        if (file_exists($file)) {
             batch_info("Product {$groupId}:{$product['product_id']} have been already shared");
             continue;
        }
        $product['price'] = app_money_format($product['price']);
        //$product['tags'] = $tags;
        $data = app_get_fb_share_content($product); 
        batch_info($product['url'] . ' shared at ' . date('Y/m/d H:i'));
        $postId = postToGroup($groupId, $data, $accessToken, $errorMessage);         
        if (!empty($postId)) {
            batch_info("Post OK {$groupId}: {$postId}"); 
            if (!file_put_contents($file, $postId)) {
                batch_info("Can not write file");
                exit;
            }
            if (!empty($product['colors']) && count($product['colors']) > 1) {
                __comment($product, $postId);
            }
        } else { 
			batch_info("---Post FAIL {$groupId}: {$errorMessage}");		
		}
    } else {                
        __comment($product, $postId);
    }
}
batch_info('Done');
exit;