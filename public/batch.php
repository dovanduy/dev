<?php
$env = 'production'; // development, production
$config = [
    'development' => [
        'timeout' => 2*60,
        'base_uri' => 'http://api.vuongquocbalo.dev',    
        'facebook_app_id' => '261013080913491',
        'facebook_app_secret' => '0eb33476da975933077a4d4ad094479b',
    ],
    'production' => [
        'timeout' => 2*60,
        'base_uri' => 'http://api.vuongquocbalo.com',    
        'facebook_app_id' => '1679604478968266',
        'facebook_app_secret' => '53bbe4bab920c2dd3bb83855a4e63a94',
    ]
];
$config = $config[$env];
function call($url, $param = array()) {
	global $config;
	$method = 'post';
	if (isset($config[$url])) {
		if (is_array($config[$url])) {
			list($url, $method) = $config[$url];        
		} else {
			$url = $config[$url];
		}
	}
	try {		
		$headers = array("Content-Type:multipart/form-data");
		$url = $config['base_uri'] . $url;
		$ch = curl_init();
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $param,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SAFE_UPLOAD => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => $config['timeout'],
		);
		curl_setopt_array($ch, $options);
		$response = curl_exec($ch);		
		$errno = curl_errno($ch);
		if (empty($errno)) {
			curl_close($ch);
			$result = json_decode($response, true);
            switch ($result['status']) {
                case 'OK';
                    return $result['results'];                   
                case 'ERROR_VALIDATION':                                         
                case 'ERROR':
                    return false;
            }
            return $result;
		}
		if (!empty($ch)) {
			@curl_close($ch);
		}
	} catch (\Exception $e) {         
		print_r($e->getMessage());
	}
	return false;
}
include ('../vendor/facebook/php-sdk-v4/src/Facebook/autoload.php');
$fb = new \Facebook\Facebook([
    'app_id' => $config['facebook_app_id'],
    'app_secret' => $config['facebook_app_secret'],
]);
list($users, $urls) = call('/users/fbadmin');
if (empty($users) || empty($urls)) {
    echo 'Empty';
    exit;
}
$tagIds = [
    '10206637393356602', // Thai Lai
    '129881887426347', // Balo Đẹp
    '835521976592060', // Ngoc Nguyen My
    '1723524741251993', // Duc Tin
   // '490650357797276', // Nguyễn Huỳnh Liên
];
foreach ($users as $user) {
    if (!in_array($user['facebook_id'], ['10206637393356602', '129881887426347'])) {
        continue;
    }
    $tags = array();
    foreach ($tagIds as $friendId) {
        if ($user['facebook_id'] != $friendId) {
            $tags[] = $friendId;
        }
    }
    foreach ($urls as $url) {
		$url = $url['url'];
        $shareData = [
            'link' => $url,
            'tags' => implode(',', $tags),
        ];
        echo PHP_EOL . $url . ' shared at ' . date('Y/m/d H:i') . PHP_EOL;
        try {
            $response = $fb->post(
                '/me/feed', 
                $shareData,
                $user['access_token']
            );
            $graphNode = $response->getGraphNode();            
            echo 'OK ID: ' . $graphNode['id'];
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'FAIL: ' . $e->getMessage(); 
            break;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'FAIL: ' . $e->getMessage();
            exit;
        } catch (\Exception $e) {
            echo 'FAIL: ' . $e->getMessage();
        }       
        sleep(6*60);
    }
    break;
}
echo PHP_EOL . 'Done';
exit;