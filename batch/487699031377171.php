<?php
include ('base.php');
$param = [
    //'no_update' => 1
];
list($users, $products) = call('/users/fbadmin', $param);
if (empty($users) || empty($products)) {
    echo 'Empty';
    exit;
}

$userId = '107846242974801'; // fb.huean@outlook.com
$groupId = '487699031377171'; // https://www.facebook.com/groups/muabansaigoncholon/

$result = array();
foreach ($products as $product) {
    $file = implode(DS, [getcwd(), $groupId, $product['product_id'] . '.txt']);
    if (file_exists($file)) {
         batch_info("Product {$groupId}:{$product['product_id']} have been already shared");
         continue;
    }
    $product['price'] = app_money_format($product['price']);
    $data = app_get_fb_share_content($product);
    batch_info($product['url'] . ' shared at ' . date('Y/m/d H:i'));
    foreach ($users as $user) {
        if ($userId != $user['facebook_id']) {
            continue;
        }        
        $id = postToGroup($groupId, $data, $user['access_token'], $errorMessage);
        if (!empty($id)) {            
            if (!file_put_contents($file, $id)) {
                batch_info("Can not write file");
                break;
            }
            batch_info("Post OK {$groupId}: {$id}");            
            if (!empty($product['colors']) && count($product['colors']) > 1) {
                foreach ($product['colors'] as $color) {   
                    sleep(rand_post_time());
                    $commentData = [
                        'message' => $product['name'] . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name'])),
                        'attachment_url' => $color['url_image'],
                    ];
                    $commentId = commentToPost($id, $commentData, $user['access_token'], $errorMessage);
                    if (!empty($commentId)) {
                        batch_info("---Comment OK {$id}: {$commentId}");
                    } else {
                        batch_info("---Comment FAIL {$id}: {$errorMessage}");
                    }
                }
            }
        } else {
            batch_info("Post FAIL {$groupId}: {$errorMessage}");
        }
    }
    sleep(rand_post_time());
}
batch_info('Done');
exit;