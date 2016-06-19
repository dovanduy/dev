<?php
include ('base.php');
$param = [
    'no_update' => 1
];
list($users, $products) = call('/users/fbadmin', $param);
if (empty($users) || empty($products)) {
    echo 'Empty';
    exit;
}

// fb_user_id => [group_id]
$fbUser = [
//    '126728971080640' => [ // kenstore2016@gmail.com
//        //'1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
//        '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
//    ],
    '103326903428052' => [ // fb.minhan@outlook.com
        '209799659176359', // Rao vặt linh tinh        
    ]
];

foreach ($products as $product) {
    if (file_put_contents(implode(DS, [getcwd(), 'products', $product['product_id'] . '.txt']), serialize($product))) {
        foreach ($users as $user) {
            if (!empty($fbUser[$user['facebook_id']])) {                
                foreach ($fbUser[$user['facebook_id']] as $groupId) {
                    $cmd = "php " . getcwd() . "/fbpost.php {$groupId} {$product['product_id']} {$user['facebook_id']} {$user['access_token']} " . OUTPUT_TO_NULL;
                    try {
                        exec($cmd);
                    } catch (\Exception $e) {
                        echo PHP_EOL . $e->getMessage();
                        exit;
                    }
                }
            }
        }
        sleep(rand(3*60, 5*60));
    }    
}
exit;

p($products, 1);
if ($env == 'development') {
    $userGroup = [
        '129746714106531' => [            
            '209799659176359', // Rao vặt linh tinh            
        ]
    ];
} else {
    $userGroup = [
        '129881887426347' => [
            '952553334783243', // Chợ online Khang Điền Q.9 https://www.facebook.com/groups/928701673904347/
            '928701673904347', // Chợ sinh viên giá rẻ https://www.facebook.com/groups/928701673904347/
            '1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
            '297906577042130', // Hội những người mê kinh doanh online
            '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
            '209799659176359', // Rao vặt linh tinh
            '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
            '487699031377171', // https://www.facebook.com/groups/muabansaigoncholon/
        ]
    ];
}
$fb = new \Facebook\Facebook([
    'app_id' => $config['facebook_app_id'],
    'app_secret' => $config['facebook_app_secret'],
    'default_graph_version' => 'v2.6',
    //'default_access_token' => $user['access_token'], // optional
]);
function postToGroup($groupId, $data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/{$groupId}/feed", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}
function commentToPost($postId, $data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/{$postId}/comments", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}
$result = array();
foreach ($products as $product) {	
    $product['price'] = app_money_format($product['price']);
    $data = app_get_fb_share_content($product);
    echo PHP_EOL . $product['url'] . ' shared at ' . date('Y/m/d H:i');
    foreach ($users as $user) {
        if (!empty($userGroup[$user['facebook_id']])) {
            foreach ($userGroup[$user['facebook_id']] as $groupId) {
                $id = postToGroup($groupId, $data, $user['access_token'], $errorMessage);
                if (!empty($id)) {
                    echo PHP_EOL . "Post OK {$groupId}: {$id}";
                    $result[] = [
                        'facebook_user_id' => $user['facebook_id'],
                        'product_id' => $product['product_id'],
                        'share_id' => $product['id'],
                        'group_id' => $groupId,
                        'post_id' => $id,
                    ];
                    if (!empty($product['colors']) && count($product['colors']) > 1) {
                        foreach ($product['colors'] as $color) {                            
                            $commentData = [
                                'message' => $product['name'] . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name'])),
                                'attachment_url' => $color['url_image'],
                            ];
                            $commentId = commentToPost($id, $commentData, $user['access_token'], $errorMessage);
                            if (!empty($id)) {
                                echo PHP_EOL . "---Comment OK {$id}: {$commentId}";
                            } else {
                                echo PHP_EOL . "---Comment FAIL {$id}: {$errorMessage}";
                            }                            
                            sleep(1.5*60);
                        }
                    }
                } else {
                    echo PHP_EOL . "Post FAIL {$groupId}: {$errorMessage}";
                }
                if ($groupId != end($userGroup[$user['facebook_id']])) {
                    sleep(1.5*60);
                }
            }
        }
    }
    sleep(2*60);
}
if (!empty($result)) {
    call('/shareurls/updatepostid', ['values' => json_encode($result)]);
}
echo PHP_EOL . 'Done';
exit;