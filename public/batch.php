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
include ('../functions.php');
$param = [
    //'no_update' => 1
];
list($users, $urls) = call('/users/fbadmin', $param);
if (empty($users) || empty($urls)) {
    echo 'Empty';
    exit;
}
$tagIds = [
    '10206637393356602', // Thai Lai
    '129881887426347', // Balo Đẹp
    '835521976592060', // Ngoc Nguyen My
    '1723524741251993', // Duc Tin
    '125965971158216', // Ken Ken
    '126728971080640', // https://www.facebook.com/kinhdothoitrang.vn
   // '490650357797276', // Nguyễn Huỳnh Liên
];
$groupIds = [
    '952553334783243', // Chợ online Khang Điền Q.9 https://www.facebook.com/groups/928701673904347/
    '928701673904347', // Chợ sinh viên giá rẻ https://www.facebook.com/groups/928701673904347/
    '1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
    '297906577042130', // Hội những người mê kinh doanh online
    '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
    '209799659176359', // Rao vặt linh tinh
    '1482043962099325', // CHỢ RAO VẶT SÀI GÒN
    '312968818826910', // CHỢ ONLINE - SÀI GÒN
    '794951187227341', // Chợ Sale Tổng Hợp BMT
    '902448306510453', // Shop rẻ cho mẹ và bé
    '109303265928424', // CHỢ SINH VIÊN HLU
];
$groupIds = [];
foreach ($users as $user) {
    if (!in_array($user['facebook_id'], ['129881887426347', '10206637393356602'])) {
        continue;
    }
    echo PHP_EOL . 'FBID: ' . $user['facebook_id'] . PHP_EOL;
    if (in_array($user['facebook_id'], ['125965971158216'])) {
        $groupIds = [
            '312968818826910', // CHỢ ONLINE - SÀI GÒN
            '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
            '209799659176359', // Rao vặt linh tinh
            '109303265928424', // CHỢ SINH VIÊN HLU
        ];
    } elseif (in_array($user['facebook_id'], ['129881887426347'])) {
        $groupIds = [
            //'952553334783243', // Chợ online Khang Điền Q.9 https://www.facebook.com/groups/928701673904347/
            //'928701673904347', // Chợ sinh viên giá rẻ https://www.facebook.com/groups/928701673904347/
            //'1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
            '297906577042130', // Hội những người mê kinh doanh online
            '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
            '209799659176359', // Rao vặt linh tinh
            '519581824789114', // CHỢ RAO VẶT & QUẢNG CÁO ONLINE
        ];
    }
    $fb = new \Facebook\Facebook([
        'app_id' => $config['facebook_app_id'],
        'app_secret' => $config['facebook_app_secret'],
        'default_graph_version' => 'v2.6',
        'default_access_token' => $user['access_token'], // optional
    ]);
    $tags = array();
    foreach ($tagIds as $friendId) {
        if ($user['facebook_id'] != $friendId) {
            $tags[] = $friendId;
        }
    }
    foreach ($urls as $url) {	
		$url['price'] = app_money_format($url['price']);
        $data = [
'message' => "{$url['name']}                                                
✓ Giá: {$url['price']}
✓ ĐT đặt hàng: 097 443 60 40 - 098 65 60 997
✓ Xem chi tiết {$url['short_url']}
✓ Khám phá thêm http://vuongquocbalo.com

CHÍNH SÁCH BÁN HÀNG:
✓ Giao hàng TOÀN QUỐC. Free ship cho đơn hàng có giá trị từ 150.000 VNĐ ở khu vực nội thành TP HCM
✓ Thanh toán khi nhận hàng
✓ Đổi trả trong 7 ngày
✓ Giao hàng từ 1 - 3 ngày
✓ Cam kết hàng giống hình
✓ Hàng chính hãng, giá luôn thấp hơn thị trường",
                        'link' => $url['url'],
                        'picture' => $url['image_facebook'],
                        'caption' => 'vuongquocbalo.com',
                        'tags' => implode(',', $tags)
                    ];
        echo PHP_EOL . $url['url'] . ' shared at ' . date('Y/m/d H:i') . PHP_EOL;
        try {
            if (!empty($groupIds)) {               
                unset($data['tags']);
                foreach ($groupIds as $groupId) {  
                    echo 'GROUP ID: ' . $groupId . PHP_EOL;
                    $response = $fb->post("/{$groupId}/feed", $data);
                    $graphNode = $response->getGraphNode();
                    if (!empty($graphNode['id'])) {                   
                        echo "OK {$groupId}: " . $graphNode['id'] .PHP_EOL;
                        sleep(3*60);
                    }
                }
            } else {
                /*
                $response = $fb->post(
                    '/me/feed', 
                    $data
                );
                $graphNode = $response->getGraphNode();  
                echo 'OK ID: ' . $graphNode['id'];
                sleep(8*60);
                * 
                */
            }
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'FAIL 1: ' . $e->getMessage(); 
            break;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'FAIL 2: ' . $e->getMessage();
            exit;
        } catch (\Exception $e) {
            echo 'FAIL 3: ' . $e->getMessage();
            exit;
        }
    }    
}
echo PHP_EOL . 'Done';
exit;