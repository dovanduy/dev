<?php
include ('base.php');
$groupId = $argv[1];
$productId = $argv[2];
$fbUserId = $argv[3];
$accessToken = $argv[4];

$content = file_get_contents(implode(DS, [getcwd(), 'products', $productId . '.txt']));
if ($content == false) {
    exit;
}
$product = unserialize($content);

$fb = new \Facebook\Facebook([
    'app_id' => $config['facebook_app_id'],
    'app_secret' => $config['facebook_app_secret'],
    'default_graph_version' => 'v2.6',
    'default_access_token' => $accessToken, // optional
]);

function postToGroup($groupId, $data, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/{$groupId}/feed", $data);
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

function commentToPost($postId, $data, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/{$postId}/comments", $data);
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
$product['price'] = app_money_format($product['price']);
$data = app_get_fb_share_content($product);
$postId = postToGroup($groupId, $data, $errorMessage);
if (file_put_contents(implode(DS, [getcwd(), 'products', $postId . '.txt']), $postId)) {
    
}
if (!empty($postId)) {
    echo PHP_EOL . "Post OK {$groupId}: {$postId}";
    $result[] = [
        'facebook_user_id' => $fbUserId,
        'product_id' => $productId,
        'share_id' => $product['id'], // share_urls.id
        'group_id' => $groupId,
        'post_id' => $postId,
    ];
    if (!empty($product['colors']) && count($product['colors']) > 1) {
        foreach ($product['colors'] as $color) {                            
            $commentData = [
                'message' => $product['name'] . ' - Màu ' . str_replace('màu', '', mb_strtolower($color['name'])),
                'attachment_url' => $color['url_image'],
            ];
            $commentId = commentToPost($postId, $commentData, $errorMessage);
            if (!empty($commentId)) {
                echo PHP_EOL . "---Comment OK {$postId}: {$commentId}";
            } else {
                echo PHP_EOL . "---Comment FAIL {$postId}: {$errorMessage}";
            }                          
            sleep(rand(3*60, 4*60));
        }
    }
} else {
    echo PHP_EOL . "Post FAIL {$groupId}: {$errorMessage}";
}
   
if (!empty($result)) {
    call('/shareurls/updatepostid', ['values' => json_encode($result)]);
}
echo PHP_EOL . 'Done';
exit;