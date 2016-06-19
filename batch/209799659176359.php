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
$userId = '103326903428052'; // fb.minhan@outlook.com
$groupId = '209799659176359'; // // Rao vặt linh tinh

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
        if ($userId == $user['facebook_id']) {
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
                        if (!empty($commentId)) {
                            echo PHP_EOL . "---Comment OK {$id}: {$commentId}";
                        } else {
                            echo PHP_EOL . "---Comment FAIL {$id}: {$errorMessage}";
                        }                            
                        sleep(randPostTime());
                    }
                }
            } else {
                echo PHP_EOL . "Post FAIL {$groupId}: {$errorMessage}";
            }            
        }
    }
    sleep(randPostTime());
}
if (!empty($result)) {
    call('/shareurls/updatepostid', ['values' => json_encode($result)]);
}
echo PHP_EOL . 'Done';
exit;